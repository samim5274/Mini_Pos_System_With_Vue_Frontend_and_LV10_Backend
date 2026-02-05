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

        $id = $request->id;

        $user = auth()->user() ?? abort(401, 'Unauthorized');

        $reg = RegGenerator::generateOrderReg($user->id)
            ?? abort(500, 'Reg number not generated.');

        $search = trim($request->id);

        return DB::transaction(function() use ($search, $reg, $user){
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
                $cartItem->increment('quantity');
            } else {
                Cart::create([
                    'reg'        => $reg,
                    'product_id' => $product->id,
                    'user_id'    => $user->id,
                    'quantity'   => 1,
                    'price'      => $product->price,
                ]);
            }

            // atomic stock reduce
            $product->decrement('stock_quantity');

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully.',
                'data'    => $product
            ], 201);
        });

    }
}
