<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utility\PayfastUtility;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Address;
use App\Models\Carrier;
use App\Models\CombinedOrder;
use App\Models\Product;
use App\Utility\PayhereUtility;
use App\Utility\NotificationUtility;
use Illuminate\Support\Facades\Config;
use Session;
use Auth;
use DB;
use function Symfony\Component\Mime\cc;

class CheckoutController extends Controller
{

    public function __construct()
    {
        $token_server_url = Config::get('key_values.token_server_url');

        $this->client = new Client(
            [
                'base_uri' => $token_server_url . "/api/",
                'timeout' => 100.0,
                'verify' => false
            ]
        );
    }

    function generateToken()
    {
        $username = Config::get('key_values.token_server_username');
        $password = Config::get('key_values.token_server_password');

        $data = array("username" => $username, "password" => $password);

        $response = $this->client->request(
            'POST',
            "auth",
            [
                'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($data)
            ]
        );

        try {
            $response = json_decode((string) $response->getBody());

            if (isset($response->token)) {
                return $response->token;
            }

            return null;
        } catch (\Throwable $th) {
            return $response;
        }
    }

    public function session_pay(Request $request)
    {

        $subtotal = 0;
        foreach (Cart::where('user_id', Auth::user()->id)->get() as $key => $cartItem){
            $product = Product::find($cartItem['product_id']);
            $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
        }
           /* if ($subtotal < get_setting('minimum_order_amount')) {
                flash(translate('You order amount is less then the minimum order amount'))->warning();
                return redirect()->route('home');
            }*/

        $token = self::generateToken();

        $sub_ids = array();


        //Try to pay
        $payment_3ds_return_domain = Config::get('key_values.payment_3ds_return_domain');
        $currency = Config::get('key_values.default_currency_code');
        $mid = Config::get('key_values.commercial_mpgs_mid');

        $order_number = mt_rand(100000, 999999);

       $address=Address::where('user_id',Auth::user()->id)->first();

            (new OrderController)->store($request);

            $request->session()->put('payment_type', 'cart_payment');

            $data['combined_order_id'] = $request->session()->get('combined_order_id');

            $request->session()->put('payment_data', $data);
             $combined_order = CombinedOrder::findOrFail($request->session()->get('combined_order_id'));
            $address_detail=json_decode($combined_order->shipping_address);
            $user_data=User::findOrFail($combined_order->user_id);

       $data = array(
            "amount" => $subtotal,
            "session" => "$request->session_id",
            "orderNumber" => $order_number,
            "currency" => "$currency",
            "bankMID" => "$mid",
            "customData" => base64_encode(Auth::user()->id),
            "secure3dResponseURL" => $payment_3ds_return_domain . "/session-pay-response/" .   base64_encode($data['combined_order_id']) ,

            "customer" => array(
                "id" => $request->session()->get('combined_order_id'),
                "email" => 'yugan91@gmail.com',
                "firstName" => $address_detail->name,
                "lastName" => $address_detail->name,
                "contactNumber" => $address_detail->phone,
                "addressLineOne" => $address_detail->address,
                "city" => $address_detail->city,
                "postalCode" => $address_detail->postal_code,
                "country" => $address_detail->country,
            )

        );

        $response = $this->client->request(
            'POST',
            "cards/pay/session3ds",
            [
                'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json', 'Authorization' => "Bearer $token"],
                'body' => json_encode($data)
            ]
        );



        $response = json_decode((string) $response->getBody());

        if (isset($response->error) && isset($response->type) && $response->error == true && $response->type == '3ds') {
            return response()->json(['response'=>$response,'order_id'=>$request->session()->get('combined_order_id')]);
        } else if (isset($response->success) || isset($response->error)) {

            $message = '';
            if (isset($response->success) && $response->success == true && isset($response->receipt)) {
                $payment_status_id = 2;
                $message = 'Initial payment at signup is approved';




            } elseif (isset($response->error) && $response->type == 'declined' && isset($response->explanation)) {
                $payment_status_id = 3;
            } else {
                $payment_status_id = 3;
            }

            $order_ref = '';
            if (isset($response->receipt)) {
                $order_ref = $response->receipt;
            }


            if (isset($response->explanation)) {
                $message = $response->explanation;
            }

            $paragraph = '';
            if ($payment_status_id == 2) {
                $paragraph = 'This is to confirm the online signup payment of ' . number_format($subtotal, 2) . ' for reference ' . $order_number . '. Your payment reference ID is: ' . $order_ref;
            } elseif ($payment_status_id == 3) {
                $paragraph = 'Online signup transaction of ' . number_format($subtotal, 2) . ' failed for reference ' . $order_number;
            }

            if ($payment_status_id == 2) {

                return response()->json(['success_proceed' => true, 'message' => $paragraph, 'payment_reference' => $subtotal], 200);

            } else if ($payment_status_id == 3) {
                return response()->json(['success_proceed' => false, 'message' => $paragraph, 'payment_reference' => $subtotal], 200);
            }

        }
    }

    function get3dsResponse(Request $request, $order_number)
    {

        $result = base64_decode($request->result3ds);

        $payment_response = json_decode($result, true);

        $order_reference = base64_decode($order_number);

        $message = '';

        $update = 'no';


            if (isset($payment_response['success']) && ($payment_response['success'] == true)) {

                $payment_status_id = 2;


                $combined_order = CombinedOrder::findOrFail($order_reference);


                foreach ($combined_order->orders as $order) {
                    $order->payment_status = 'paid';
                    $order->payment_details = json_encode($payment_response);
                    $order->save();
                }
                return response()->json(['success'=>200,'payment_status_id'=>2,'order_reference'=>$order_reference]);
            } else {

                $payment_status_id = 3;
                $combined_order = CombinedOrder::findOrFail($order_reference);


                foreach ($combined_order->orders as $order) {
                    $order->payment_status = 'failed';
                    $order->payment_details = json_encode($payment_response);
                    $order->save();
                }
            }
        return response()->json(['success'=>200,'payment_status_id'=>3,'order_reference'=>$order_reference]);

    }

    public function getPaymentStatus(Request $request)
    {
        $order_id=$request->session()->get('combined_order_id');
        $combined_order = Order::where('combined_order_id',$order_id)->first();



        if ($combined_order->payment_status==  'paid') {
            return response()->json(['redirect'=>true]);
        }else if($combined_order->payment_status==  'failed'){
            return response()->json(['redirect'=>false]);
        }
    }

    //check the selected payment gateway and redirect to that controller accordingly
    public function checkout(Request $request)
    {

        // Minumum order amount check
        if(get_setting('minimum_order_amount_check') == 1){
            $subtotal = 0;
            foreach (Cart::where('user_id', Auth::user()->id)->get() as $key => $cartItem){ 
                $product = Product::find($cartItem['product_id']);
                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
            }
            if ($subtotal < get_setting('minimum_order_amount')) {
                flash(translate('You order amount is less then the minimum order amount'))->warning();
                return redirect()->route('home');
            }
        }
        // Minumum order amount check end
        
        if ($request->payment_option != null) {
            (new OrderController)->store($request);

            $request->session()->put('payment_type', 'cart_payment');
            
            $data['combined_order_id'] = $request->session()->get('combined_order_id');
            $request->session()->put('payment_data', $data);

            if ($request->session()->get('combined_order_id') != null) {

                // If block for Online payment, wallet and cash on delivery. Else block for Offline payment
                $decorator = __NAMESPACE__ . '\\Payment\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $request->payment_option))) . "Controller";
                //dd($decorator);
                if (class_exists($decorator)) {
                    $combined_order = CombinedOrder::findOrFail($request->session()->get('combined_order_id'));
                    $manual_payment_data = array(
                        'name'   => $request->payment_option,
                        'amount' => $combined_order->grand_total,
                        'trx_id' => $request->trx_id,
                        'photo'  => $request->photo
                    );

                    }
                    return (new $decorator)->redirectToWebxpay($request,$request->session()->get('combined_order_id')
                        ,$combined_order,$manual_payment_data
                        );
                }
                else {
                    $combined_order = CombinedOrder::findOrFail($request->session()->get('combined_order_id'));
                    $manual_payment_data = array(
                        'name'   => $request->payment_option,
                        'amount' => $combined_order->grand_total,
                        'trx_id' => $request->trx_id,
                        'photo'  => $request->photo
                    );
                    foreach ($combined_order->orders as $order) {
                        $order->manual_payment = 1;
                        $order->manual_payment_data = json_encode($manual_payment_data);
                        $order->save();
                    }
                    flash(translate('Your order has been placed successfully. Please submit payment information from purchase history'))->success();
                    return redirect()->route('order_confirmed');
                }

        } else {
            flash(translate('Select Payment Option.'))->warning();
            return back();
        }
    }

    //redirects to this method after a successfull checkout
    public function checkout_done($combined_order_id, $payment)
    {
        $combined_order = CombinedOrder::findOrFail($combined_order_id);

        foreach ($combined_order->orders as $key => $order) {
            $order = Order::findOrFail($order->id);
            $order->payment_status = 'paid';
            $order->payment_details = $payment;
            $order->save();

            calculateCommissionAffilationClubPoint($order);
        }
        Session::put('combined_order_id', $combined_order_id);
        return redirect()->route('order_confirmed');
    }

    public function get_shipping_info(Request $request)
    {
        $carts = Cart::where('user_id', Auth::user()->id)->get();
//        if (Session::has('cart') && count(Session::get('cart')) > 0) {
        if ($carts && count($carts) > 0) {
            $categories = Category::all();
            return view('frontend.shipping_info', compact('categories', 'carts'));
        }
        flash(translate('Your cart is empty'))->success();
        return back();
    }

    public function store_shipping_info(Request $request)
    {
        if ($request->address_id == null) {
            flash(translate("Please add shipping address"))->warning();
            return back();
        }

        $carts = Cart::where('user_id', Auth::user()->id)->get();
        if($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('home');
        }

        foreach ($carts as $key => $cartItem) {
            $cartItem->address_id = $request->address_id;
            $cartItem->save();
        }

        $carrier_list = array();
        if(get_setting('shipping_type') == 'carrier_wise_shipping'){
            $zone = \App\Models\Country::where('id',$carts[0]['address']['country_id'])->first()->zone_id;

            $carrier_query = Carrier::query();
            $carrier_query->whereIn('id',function ($query) use ($zone) {
                $query->select('carrier_id')->from('carrier_range_prices')
                ->where('zone_id', $zone);
            })->orWhere('free_shipping', 1);
            $carrier_list = $carrier_query->get();
        }
        
        return view('frontend.delivery_info', compact('carts','carrier_list'));
    }

    public function store_delivery_info(Request $request)
    {
        $carts = Cart::where('user_id', Auth::user()->id)
                ->get();

        if($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('home');
        }

        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();
        $total = 0;
        $tax = 0;
        $shipping = 0;
        $subtotal = 0;

        if ($carts && count($carts) > 0) {
            foreach ($carts as $key => $cartItem) {
                $product = Product::find($cartItem['product_id']);
                $tax += cart_product_tax($cartItem, $product,false) * $cartItem['quantity'];
                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];

                if(get_setting('shipping_type') != 'carrier_wise_shipping' || $request['shipping_type_' . $product->user_id] == 'pickup_point'){
                    if ($request['shipping_type_' . $product->user_id] == 'pickup_point') {
                        $cartItem['shipping_type'] = 'pickup_point';
                        $cartItem['pickup_point'] = $request['pickup_point_id_' . $product->user_id];
                    } else {
                        $cartItem['shipping_type'] = 'home_delivery';
                    }
                    $cartItem['shipping_cost'] = 0;
                    if ($cartItem['shipping_type'] == 'home_delivery') {
                        $cartItem['shipping_cost'] = getShippingCost($carts, $key);
                    }
                }
                else{
                    $cartItem['shipping_type'] = 'carrier';
                    $cartItem['carrier_id'] = $request['carrier_id_' . $product->user_id];
                    $cartItem['shipping_cost'] = getShippingCost($carts, $key, $cartItem['carrier_id']);
                }

                $shipping += $cartItem['shipping_cost'];
                $cartItem->save();
            }
            $total = $subtotal + $tax + $shipping;

            return view('frontend.payment_select', compact('carts', 'shipping_info', 'total'));

        } else {
            flash(translate('Your Cart was empty'))->warning();
            return redirect()->route('home');
        }
    }

    public function apply_coupon_code(Request $request)
    {
        $coupon = Coupon::where('code', $request->code)->first();
        $response_message = array();

        if ($coupon != null) {
            if (strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date) {
                if (CouponUsage::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->first() == null) {
                    $coupon_details = json_decode($coupon->details);

                    $carts = Cart::where('user_id', Auth::user()->id)
                                    ->where('owner_id', $coupon->user_id)
                                    ->get();

                    $coupon_discount = 0;
                    
                    if ($coupon->type == 'cart_base') {
                        $subtotal = 0;
                        $tax = 0;
                        $shipping = 0;
                        foreach ($carts as $key => $cartItem) { 
                            $product = Product::find($cartItem['product_id']);
                            $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                            $tax += cart_product_tax($cartItem, $product,false) * $cartItem['quantity'];
                            $shipping += $cartItem['shipping_cost'];
                        }
                        $sum = $subtotal + $tax + $shipping;
                        if ($sum >= $coupon_details->min_buy) {
                            if ($coupon->discount_type == 'percent') {
                                $coupon_discount = ($sum * $coupon->discount) / 100;
                                if ($coupon_discount > $coupon_details->max_discount) {
                                    $coupon_discount = $coupon_details->max_discount;
                                }
                            } elseif ($coupon->discount_type == 'amount') {
                                $coupon_discount = $coupon->discount;
                            }

                        }
                    } elseif ($coupon->type == 'product_base') {
                        foreach ($carts as $key => $cartItem) { 
                            $product = Product::find($cartItem['product_id']);
                            foreach ($coupon_details as $key => $coupon_detail) {
                                if ($coupon_detail->product_id == $cartItem['product_id']) {
                                    if ($coupon->discount_type == 'percent') {
                                        $coupon_discount += (cart_product_price($cartItem, $product, false, false) * $coupon->discount / 100) * $cartItem['quantity'];
                                    } elseif ($coupon->discount_type == 'amount') {
                                        $coupon_discount += $coupon->discount * $cartItem['quantity'];
                                    }
                                }
                            }
                        }
                    }

                    if($coupon_discount > 0){
                        Cart::where('user_id', Auth::user()->id)
                            ->where('owner_id', $coupon->user_id)
                            ->update(
                                [
                                    'discount' => $coupon_discount / count($carts),
                                    'coupon_code' => $request->code,
                                    'coupon_applied' => 1
                                ]
                            );
                        $response_message['response'] = 'success';
                        $response_message['message'] = translate('Coupon has been applied');
                    }
                    else{
                        $response_message['response'] = 'warning';
                        $response_message['message'] = translate('This coupon is not applicable to your cart products!');
                    }
                    
                } else {
                    $response_message['response'] = 'warning';
                    $response_message['message'] = translate('You already used this coupon!');
                }
            } else {
                $response_message['response'] = 'warning';
                $response_message['message'] = translate('Coupon expired!');
            }
        } else {
            $response_message['response'] = 'danger';
            $response_message['message'] = translate('Invalid coupon!');
        }

        $carts = Cart::where('user_id', Auth::user()->id)
                ->get();
        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();

        $returnHTML = view('frontend.partials.cart_summary', compact('coupon', 'carts', 'shipping_info'))->render();
        return response()->json(array('response_message' => $response_message, 'html'=>$returnHTML));
    }

    public function remove_coupon_code(Request $request)
    {
        Cart::where('user_id', Auth::user()->id)
                ->update(
                        [
                            'discount' => 0.00,
                            'coupon_code' => '',
                            'coupon_applied' => 0
                        ]
        );

        $coupon = Coupon::where('code', $request->code)->first();
        $carts = Cart::where('user_id', Auth::user()->id)
                ->get();

        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();

        return view('frontend.partials.cart_summary', compact('coupon', 'carts', 'shipping_info'));
    }

    public function apply_club_point(Request $request) {
        if (addon_is_activated('club_point')){

            $point = $request->point;

            if(Auth::user()->point_balance >= $point) {
                $request->session()->put('club_point', $point);
                flash(translate('Point has been redeemed'))->success();
            }
            else {
                flash(translate('Invalid point!'))->warning();
            }
        }
        return back();
    }

    public function remove_club_point(Request $request) {
        $request->session()->forget('club_point');
        return back();
    }

    public function order_confirmed()
    {
        $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));

        Cart::where('user_id', $combined_order->user_id)
                ->delete();

        //Session::forget('club_point');
        //Session::forget('combined_order_id');
        
        // foreach($combined_order->orders as $order){
        //     NotificationUtility::sendOrderPlacedNotification($order);
        // }

        return view('frontend.order_confirmed', compact('combined_order'));
    }

    function generateRandomString($length = 15)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function onlinePayment(Request $request)
    {
        $random = 'ACS' . self::generateRandomString();
        $old_amount = 100;
        $new_amount = $old_amount*100 / (100 - 2.6694);

          /*  if (Auth::check()) {
                $user_id = Auth::user()->id;
            } else {
                $user_id = null;
            }*/
        //	dd($user_id);
       /* DB::table('stc_form')->insert(
            [
                'Name' => $request->name,
                'contact_no' => $request->contact_no,
                'email' => $request->email,
                'batch' => $request->batch,
                'donor_category' => $request->donor_category,
                'payment_type' => 1,
                'user_id' => $user_id,
                'bank_charge' => $new_amount - $old_amount,
                'paid_amount' => $old_amount,
                'amount' => $new_amount,
                'order_id' => $random,
                'date_of_payment' => date('Y-m-d'),
            ]
        );*/

        $Name = 'Yuga';
        $contact_no = '0111225112';
        $email = 'yugan91@gmail.com';
        $batch = 'NEw';
        $payment_type = $request->payment_type;
        $project = $request->project;
        $amount = number_format((float)$new_amount, 2, '.', '');

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        $publickey = base64_decode(Config::get('key_values.public_key'));
        $url =Config::get('key_values.webx_url');
        $plaintext = $random.'|'.$amount;

        openssl_public_encrypt($plaintext, $encrypt, $publickey);
        $secret_key=Config::get('key_values.secret_key');
        $payment = base64_encode($encrypt);
        //dd($payment);
        return view('payment',
            compact('Name', 'contact_no', 'email', 'batch', 'payment_type', 'amount', 'random','publickey','payment','url','secret_key'));

    }
    
    
}
