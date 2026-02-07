<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Services\RegGenerator;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;

class OrderController extends Controller
{
    public function confirmOrder(Request $request){

        $userId = auth()->id();
        $reg = RegGenerator::generateOrderReg($userId);

        return DB::transaction(function() use ($reg, $userId, $request) {
            // check reg valid or not
            if($reg == $request->reg){

                $cartItems = Cart::where("reg", $reg)->where('user_id', $userId) ->get();
                if ($cartItems->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No cart data found for this reg.'
                    ], 404);
                }

                $order = Order::where('reg', $reg)->where('user_id', $userId)->lockForUpdate()->exists();
                if ($order) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This order already placed.'
                    ], 409);
                }

                $total = 0;
                foreach($cartItems as $item){
                    $total += $item->quantity * $item->product->price;
                }

                Order::create([
                    'reg'       => $reg,
                    'date'      => now()->toDateString(),
                    'user_id'   => $userId,
                    'status'    => 'Pending',
                    'total'     => $total
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Order confirm successfully. Reg: ". $request->reg,
                    'data' => $cartItems,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => "Reg not match.",
            ]);
        });
    }
}
