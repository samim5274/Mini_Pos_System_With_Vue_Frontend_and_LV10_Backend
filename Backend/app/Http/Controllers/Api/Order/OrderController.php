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
use App\Library\SslCommerz\SslCommerzNotification;

class OrderController extends Controller
{
    public function orderList(){

        $userId = auth()->id();

        $today = now()->toDateString();
        
        $orders = Order::where('user_id', $userId)->whereDate('date', now())->latest()->get();
        if(!$orders){
            return response()->json([
                'success' => false,
                'message' => 'No orders found for today.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => "Get all order list.",
            'data' => $orders,
        ], 200);
    }

    public function confirmOrder(Request $request){

        $userId = auth()->id();
        $reg = RegGenerator::generateOrderReg($userId);

        return DB::transaction(function() use ($reg, $userId, $request) {
            // check reg valid or not
            if($reg == $request->reg){

                $cartItems = Cart::where("reg", $reg)->with('product')->where('user_id', $userId) ->get();
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

                $order = Order::create([
                    'reg'       => $reg,
                    'date'      => now()->toDateString(),
                    'user_id'   => $userId,
                    'status'    => 'unpaid',
                    'total'     => $total
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Order confirm successfully. Reg: ". $request->reg,
                    'data' => $order,
                    'order_id' => $order->id,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => "Reg not match.",
            ]);
        });
    }

    public function getTotal($id){
        $order = Order::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'data' => $order,
            'total' => (float) $order->total,
        ]);
    }

    public function pay(Request $request)
    {
        $request->validate([
            'reg' => ['required', 'string'],
        ]);

        $userId = auth()->id();

        return DB::transaction(function () use ($request, $userId) {
            
            $order = Order::where('reg', $request->reg)->where('user_id', $userId)->lockForUpdate()->firstOrFail();

            // already paid?
            if ($order->status === 'Paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order already paid.',
                ], 409);
            }

            $amount = (float) $order->total;
            if ($amount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No due payment.',
                ], 409);
            }

            $tranId = 'TRX-' . $order->reg . '-' . Str::upper(Str::random(6));

            $order->transaction_id = $tranId;
            $order->status = 'Pending';
            $order->save();

            $user = Auth::user();

            $post_data = [];
            $post_data['total_amount'] = $amount;
            $post_data['currency'] = "BDT";
            $post_data['tran_id'] = $tranId;

            // callbacks (no auth middleware on these routes)
            $post_data['success_url'] = url('/api/payment/success');
            $post_data['fail_url']    = url('/api/payment/fail');
            $post_data['cancel_url']  = url('/api/payment/cancel');
            $post_data['ipn_url']     = url('/api/payment/ipn');

            // customer info
            $post_data['cus_name']  = $user->name ?? 'Customer';
            $post_data['cus_email'] = $user->email ?? 'customer@mail.com';
            $post_data['cus_add1']  = 'Dhaka';
            $post_data['cus_city']  = 'Dhaka';
            $post_data['cus_country'] = 'Bangladesh';
            $post_data['cus_phone'] = $user->phone ?? '01700000000';

            // product info
            $post_data['shipping_method']  = "NO";
            $post_data['product_name']     = "Order Payment";
            $post_data['product_category'] = "Goods";
            $post_data['product_profile']  = "general";

            // optional values
            $post_data['value_a'] = (string) $order->id;
            $post_data['value_b'] = (string) $order->reg;

            $sslc = new SslCommerzNotification();

            // return json (best for SPA)
            $payment = $sslc->makePayment($post_data, 'checkout', 'json');

            if (is_string($payment)) {
                $payment = json_decode($payment, true) ?? [];
            }

            $gatewayUrl = $payment['GatewayPageURL'] ?? $payment['data'] ?? null;

            if (!$gatewayUrl) {
                // rollback status if gateway url missing
                $order->status = 'unpaid';
                $order->transaction_id = null;
                $order->save();

                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway init failed.',
                    'raw' => $payment,
                    'app_url' => config('app.url'),
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Redirecting to payment gateway...',
                'gateway_url' => $gatewayUrl,
                'tran_id' => $tranId,
                'order_id' => $order->id,
            ], 200);
        });
    }

    public function payViaAjax(Request $request)
    {

        # Here you have to receive all the order data to initate the payment.
        # Lets your oder trnsaction informations are saving in a table called "orders"
        # In orders table order uniq identity is "transaction_id","status" field contain status of the transaction, "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.

        $post_data = array();
        $post_data['total_amount'] = '10'; # You cant not pay less than 10
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = uniqid(); // tran_id must be unique

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = 'Customer Name';
        $post_data['cus_email'] = 'customer@mail.com';
        $post_data['cus_add1'] = 'Customer Address';
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = "";
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = "";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = '8801XXXXXXXXX';
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Store Test";
        $post_data['ship_add1'] = "Dhaka";
        $post_data['ship_add2'] = "Dhaka";
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_state'] = "Dhaka";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_phone'] = "";
        $post_data['ship_country'] = "Bangladesh";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Computer";
        $post_data['product_category'] = "Goods";
        $post_data['product_profile'] = "physical-goods";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = "ref001";
        $post_data['value_b'] = "ref002";
        $post_data['value_c'] = "ref003";
        $post_data['value_d'] = "ref004";


        #Before  going to initiate the payment order status need to update as Pending.
        $update_product = DB::table('orders')
            ->where('transaction_id', $post_data['tran_id'])
            ->updateOrInsert([
                'name' => $post_data['cus_name'],
                'email' => $post_data['cus_email'],
                'phone' => $post_data['cus_phone'],
                'amount' => $post_data['total_amount'],
                'status' => 'Pending',
                'address' => $post_data['cus_add1'],
                'transaction_id' => $post_data['tran_id'],
                'currency' => $post_data['currency']
            ]);

        $sslc = new SslCommerzNotification();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        $payment_options = $sslc->makePayment($post_data, 'checkout', 'json');

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }

    }

    public function success(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $amount  = $request->input('amount');
        $currency= $request->input('currency');

        $order = Order::where('transaction_id', $tran_id)->first();
        if (!$order) return response("Invalid Transaction", 404);

        if (in_array($order->status, ['Paid', 'processing'])) {
            return response("Already Completed", 200);
        }

        $sslc = new SslCommerzNotification();
        $isValid = $sslc->orderValidate($request->all(), $tran_id, $amount, $currency);

        if ($isValid) {
            $order->status  = 'Paid';
            $order->paid_at = now();
            $order->save();

            return redirect(config('app.frontend_url') . '/order');
        }

        return redirect(config('app.frontend_url') . '/order?status=failed');
    }


    public function fail(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $order = Order::where('transaction_id', $tran_id)->first();

        if ($order && $order->status === 'Pending') {
            $order->status = 'failed';
            $order->save();
        }

        return response("Payment Failed", 200);
    }


    public function cancel(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $order = Order::where('transaction_id', $tran_id)->first();

        if ($order && $order->status === 'Pending') {
            $order->status = 'Cancelled';
            $order->save();
        }

        return response("Payment Cancelled", 200);
    }


    public function ipn(Request $request)
    {
        $tran_id = $request->input('tran_id');
        if (!$tran_id) return response("Invalid Data", 400);

        $order = Order::where('transaction_id', $tran_id)->first();
        if (!$order) return response("Invalid Transaction", 404);

        if ($order->status !== 'Pending') return response("Already Updated", 200);

        $sslc = new SslCommerzNotification();
        $isValid = $sslc->orderValidate($request->all(), $tran_id, $order->total, 'BDT');

        if ($isValid) {
            $order->status  = 'Paid';
            $order->paid_at = now();
            $order->save();

            return response("IPN: Payment Completed", 200);
        }

        return response("IPN: Validation Failed", 400);
    }

}
