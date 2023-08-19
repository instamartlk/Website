<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\CombinedOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class WebxpayController extends Controller
{
    /**
     * Redirect the user to the Webxpay payment page.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToWebxpay($request,$order_id,$combined_order,$manual_payment_data)
    {
        //dd($combined_order);
        // Retrieve necessary payment data from the request
        $amount = $request->input('amount');
        $orderId = $request->input('order_id');
        // Other relevant payment details

        // Prepare payload for Webxpay API
        $payload = [
            'amount' => $amount,
            'order_id' => $orderId,
            // Add other required parameters
        ];

        $Name = 'Yuga';
        $contact_no = '0111225112';
        $email = 'yugan91@gmail.com';
        $batch = 'New';
        $payment_type = $request->payment_type;
        $project = $request->project;
        $amount = number_format((float)$combined_order['grand_total'], 2, '.', '');

        $publickey = base64_decode(Config::get('key_values.public_key'));
        $url =Config::get('key_values.webx_url');
        $plaintext = $order_id.'|'.$amount;

        openssl_public_encrypt($plaintext, $encrypt, $publickey);
        $secret_key=Config::get('key_values.secret_key');
        $payment = base64_encode($encrypt);
        $manual_payment_data=json_encode($manual_payment_data);
        $combined_order=json_encode($combined_order);
        $custom_fields = base64_encode($manual_payment_data.'|'.$combined_order);


        //dd($payment);
        return view('payment',
            compact('Name', 'contact_no', 'email', 'batch', 'payment_type', 'amount','publickey','payment',
                'url','secret_key','manual_payment_data','custom_fields','custom_fields'));

    }


    /**
     * Handle the callback from Webxpay after payment completion.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function webxpayCallback(Request $request)
    {
        $publickey = base64_decode(Config::get('key_values.public_key'));
        $payment = base64_decode($request ["payment"]);
        $signature = base64_decode($request ["signature"]);
        $custom_fields = base64_decode($request ["custom_fields"]);
//load public key for signature matching


        openssl_public_decrypt($signature, $value, $publickey);

        $responseVariables = explode('|', $payment);

        $combined_order = CombinedOrder::findOrFail($responseVariables[0]);

        foreach ($combined_order->orders as $order) {
            $order->payment_status = 'paid';
            $order->payment_details = json_encode($payment);
            $order->save();
        }

        flash(translate('Your order has been placed successfully. Please submit payment information from purchase history'))->success();
        return redirect()->route('order_confirmed');
    }
}
