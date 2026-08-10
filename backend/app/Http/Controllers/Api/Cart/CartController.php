<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Product;
use App\Models\Cart;
use App\Services\RegGenerator;

class CartController extends Controller
{
    public function index(){
        
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'message' => 'Unauthorized user',
                'reg'     => null,
                'data'    => [],
            ], 401);
        }

        $reg = RegGenerator::generateOrderReg($userId);
        
        $items = Cart::with(['product','user'])
            ->where('user_id', $userId)
            ->where('reg', $reg)
            ->get();

        return response()->json([
            'message' => 'Cart items',
            'reg' => $reg,     
            'data' => $items
        ], 200);
    }

    public function addToCart(Request $request){

        $search = trim($request->search);
        $qty    = (int) ($request->qty ?? 1);

        $user = auth()->user() ?? abort(401, 'Unauthorized');

        $reg = RegGenerator::generateOrderReg($user->id)
            ?? abort(500, 'Reg number not generated.');

        return DB::transaction(function() use ($search, $reg, $user, $qty){
            $product = Product::where(function ($q) use ($search) {

                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }

                $q->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");

            })->lockForUpdate()->firstOrFail();

            // stock check
            if ($product->stock_quantity <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Out of stock.'
                ], 409);
            }

            $cartItem = Cart::where('reg', $reg)->where('product_id', $product->id)->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $qty);
            } else {
                Cart::create([
                    'reg'        => $reg,
                    'product_id' => $product->id,
                    'user_id'    => $user->id,
                    'quantity'   => $qty,
                    'price'      => $product->price,
                ]);
            }

            // atomic stock reduce
            $product->decrement('stock_quantity', $qty);

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully.',
                'data'    => $product
            ], 201);
        });

    }

    public function removeItem(Request $request, $reg, $id)
    {
        // reg / id guard
        if (empty($reg) || empty($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Reg and product id is missing.',
            ], 422);
        }

        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized user',
            ], 401);
        }

        $product = Product::where('id', $id)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in Product table.',
            ], 404);
        }

        $cartItem = Cart::where('user_id', $userId)->where('reg', $reg)
                        ->where('product_id', $id)->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart.',
            ], 404);
        }

        if($cartItem->quantity > 0){
            $product->stock_quantity += $cartItem->quantity;
            $product->save();            
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart item removed successfully.',
        ], 200);
    }

    public function updateQty(Request $request, $reg, $product_id){
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($data, $reg, $product_id){

            // 1) Cart row loack
            $cart = Cart::where('reg', $reg)->where('product_id', $product_id)
                        ->where('user_id', auth()->id())->lockForUpdate()->firstOrFail();

            $oldQty = (int) $cart->quantity;
            $newQty = (int) $data['quantity'];

            if($newQty === $oldQty){
                return response()->json([
                    'success' => true,
                    'message' => 'Qty no change.',
                    'quantity' => $cart->quantity,
                ]);
            }

            // different calculate
            $diff = $newQty - $oldQty;
            // diff > 0 => customer more qty => stock (-)
            // diff < 0 => customer less qty => stock (+)

            // 2) Product row loack
            $product = Product::lockForUpdate()->where('id', $product_id)->firstOrFail();

            // 3) Stock check + update
            if($diff > 0){
                // when update stock then need available stock in product
                if($product->stock_quantity < $diff){
                    return response()->json([
                        'success' => false,
                        'message' => 'Out of stock',
                        'available_stock' => $product->stock_quantity,
                    ], 422);
                } else {
                    $product->stock_quantity -= $diff;
                }
            } else {
                $product->stock_quantity += abs($diff);
            }

            // 4) Save both
            $cart->quantity = $newQty;

            $product->save();
            $cart->save();

            return response()->json([
                'success' => true,
                'quantity' => $cart->quantity,
                'stock' => $product->stock_quantity,
            ]);

        });
    }
}
