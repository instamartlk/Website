@extends('frontend.layouts.app')

@section('content')

    <!-- Steps -->
    <section class="pt-5 mb-4">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="row gutters-5 sm-gutters-10">
                        <div class="col done">
                            <div class="text-center border border-bottom-6px p-2 text-success">
                                <i class="la-3x mb-2 las la-shopping-cart"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('1. My Cart') }}</h3>
                            </div>
                        </div>
                        <div class="col done">
                            <div class="text-center border border-bottom-6px p-2 text-success">
                                <i class="la-3x mb-2 las la-map"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('2. Shipping info') }}
                                </h3>
                            </div>
                        </div>
                        <div class="col done">
                            <div class="text-center border border-bottom-6px p-2 text-success">
                                <i class="la-3x mb-2 las la-truck"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('3. Delivery info') }}
                                </h3>
                            </div>
                        </div>
                        <div class="col active">
                            <div class="text-center border border-bottom-6px p-2 text-primary">
                                <i class="la-3x mb-2 las la-credit-card cart-animate"
                                   style="margin-right: -100px; transition: 2s;"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('4. Payment') }}</h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center border border-bottom-6px p-2">
                                <i class="la-3x mb-2 opacity-50 las la-check-circle"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('5. Confirmation') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Payment Info -->
    <section class="mb-4">
        <div class="container text-left">
            <div class="row">
                <div class="col-lg-8">
                    <form action="{{ route('payment.checkout') }}" class="form-default" role="form" method="POST"
                          id="checkout-form">
                        @csrf
                        <input type="hidden" name="owner_id" value="{{ $carts[0]['owner_id'] }}">

                        <div class="card rounded-0 border shadow-none">
                            <!-- Additional Info -->
                            <div class="card-header p-4 border-bottom-0">
                                <h3 class="fs-16 fw-700 text-dark mb-0">
                                    {{ translate('Any additional info?') }}
                                </h3>
                            </div>
                            <div class="form-group px-4">
                                <textarea name="additional_info" rows="5" class="form-control rounded-0"
                                          placeholder="{{ translate('Type your text...') }}"></textarea>
                            </div>

                            <div class="card-header p-4 border-bottom-0">
                                <h3 class="fs-16 fw-700 text-dark mb-0">
                                    {{ translate('Select a payment option') }}
                                </h3>
                            </div>
                            <!-- Payment Options -->
                            <div class="card-body text-center px-4 pt-0">
                                <div class="row gutters-10">
                                    <!-- Paypal -->
                                    @if (get_setting('paypal_payment') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="paypal" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/paypal.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('Paypal') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!--Stripe -->
                                    @if (get_setting('stripe_payment') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="stripe" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/stripe.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('Stripe') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- Mercadopago -->
                                    @if (get_setting('mercadopago_payment') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="mercadopago" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/mercadopago.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('Mercadopago') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- sslcommerz -->
                                    @if (get_setting('sslcommerz_payment') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="sslcommerz" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/sslcommerz.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('sslcommerz') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- instamojo -->
                                    @if (get_setting('instamojo_payment') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="instamojo" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/instamojo.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('Instamojo') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- razorpay -->
                                    @if (get_setting('razorpay') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="razorpay" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/rozarpay.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('Razorpay') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- paystack -->
                                    @if (get_setting('paystack') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="paystack" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/paystack.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('Paystack') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- voguepay -->
                                    @if (get_setting('voguepay') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="voguepay" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/vogue.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('VoguePay') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- payhere -->
                                    @if (get_setting('payhere') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="payhere" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/payhere.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('payhere') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- ngenius -->
                                    @if (get_setting('ngenius') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="ngenius" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/ngenius.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('ngenius') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- iyzico -->
                                    @if (get_setting('iyzico') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="iyzico" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/iyzico.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('Iyzico') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- nagad -->
                                    @if (get_setting('nagad') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="nagad" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/nagad.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('Nagad') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- bkash -->
                                    @if (get_setting('bkash') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="bkash" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/bkash.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('Bkash') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- aamarpay -->
                                    @if (get_setting('aamarpay') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="aamarpay" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/aamarpay.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('Aamarpay') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- authorizenet -->
                                    @if (get_setting('authorizenet') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="authorizenet" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/authorizenet.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('Authorize Net') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- payku -->
                                    @if (get_setting('payku') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="payku" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/payku.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('Payku') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- African Payment Getaway -->
                                    @if (addon_is_activated('african_pg'))
                                    <!-- flutterwave -->
                                        @if (get_setting('flutterwave') == 1)
                                            <div class="col-6 col-xl-3 col-md-4">
                                                <label class="aiz-megabox d-block mb-3">
                                                    <input value="flutterwave" class="online_payment"
                                                           type="radio" name="payment_option" checked>
                                                    <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                        <img src="{{ static_asset('assets/img/cards/flutterwave.png') }}"
                                                             class="img-fit mb-2">
                                                        <span class="d-block text-center">
                                                            <span
                                                                    class="d-block fw-600 fs-15">{{ translate('flutterwave') }}</span>
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>
                                        @endif
                                    <!-- payfast -->
                                        @if (get_setting('payfast') == 1)
                                            <div class="col-6 col-xl-3 col-md-4">
                                                <label class="aiz-megabox d-block mb-3">
                                                    <input value="payfast" class="online_payment" type="radio"
                                                           name="payment_option" checked>
                                                    <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                        <img src="{{ static_asset('assets/img/cards/payfast.png') }}"
                                                             class="img-fit mb-2">
                                                        <span class="d-block text-center">
                                                            <span
                                                                    class="d-block fw-600 fs-15">{{ translate('payfast') }}</span>
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                <!--paytm -->
                                    @if (addon_is_activated('paytm') && get_setting('paytm_payment') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="paytm" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/paytm.jpg') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('Paytm') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- toyyibpay -->
                                    @if (addon_is_activated('paytm') && get_setting('toyyibpay_payment') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="toyyibpay" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/toyyibpay.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('ToyyibPay') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- myfatoorah -->
                                    @if (addon_is_activated('paytm') && get_setting('myfatoorah') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="myfatoorah" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                    <img src="{{ static_asset('assets/img/cards/myfatoorah.png') }}"
                                                         class="img-fit mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('MyFatoorah') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                <!-- khalti -->
                                    @if (addon_is_activated('paytm') && get_setting('khalti_payment') == 1)
                                        <div class="col-6 col-xl-3 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="Khalti" class="online_payment" type="radio"
                                                       name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ static_asset('assets/img/cards/khalti.png') }}"
                                                         class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">{{ translate('Khalti') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif

                                    <div class="col-6 col-xl-3 col-md-4">
                                        <label class="aiz-megabox d-block mb-3">
                                            <input value="online" class="online_payment" type="radio"
                                                   name="payment_option" >
                                            <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ static_asset('payx.jpg') }}"
                                                         class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">Pay Online</span>
                                                    </span>
                                                </span>
                                        </label>
                                    </div>

                                    <div class="col-6 col-xl-3 col-md-4">
                                        <label class="aiz-megabox d-block mb-3">
                                            <input value="webxpay" class="online_payment_other" type="radio"
                                                   name="payment_option" >
                                            <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ static_asset('alternate.jpg') }}"
                                                         class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span
                                                                class="d-block fw-600 fs-15">Other Payment Options</span>
                                                    </span>
                                                </span>
                                        </label>
                                    </div>

                                    <!-- Cash Payment -->
                                    @if (get_setting('cash_payment') == 1)
                                        @php
                                            $digital = 0;
                                            $cod_on = 1;
                                            foreach ($carts as $cartItem) {
                                                $product = \App\Models\Product::find($cartItem['product_id']);
                                                if ($product['digital'] == 1) {
                                                    $digital = 1;
                                                }
                                                if ($product['cash_on_delivery'] == 0) {
                                                    $cod_on = 0;
                                                }
                                            }
                                        @endphp
                                        @if ($digital != 1 && $cod_on == 1)
                                            <div class="col-6 col-xl-3 col-md-4">
                                                <label class="aiz-megabox d-block mb-3">
                                                    <input value="cash_on_delivery" class="online_payment"
                                                           type="radio" name="payment_option" >
                                                    <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                        <img src="{{ static_asset('assets/img/cards/cod.png') }}"
                                                             class="img-fit mb-2">
                                                        <span class="d-block text-center">
                                                            <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Cash on Delivery') }}</span>
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                    @if (Auth::check())
                                    <!-- Offline Payment -->
                                        @if (addon_is_activated('offline_payment'))
                                            @foreach (\App\Models\ManualPaymentMethod::all() as $method)
                                                <div class="col-6 col-xl-3 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="{{ $method->heading }}" type="radio"
                                                               name="payment_option" class="offline_payment_option"
                                                               onchange="toggleManualPaymentData({{ $method->id }})"
                                                               data-id="{{ $method->id }}" checked>
                                                        <span class="d-block aiz-megabox-elem rounded-0 p-3">
                                                            <img src="{{ uploaded_asset($method->photo) }}"
                                                                 class="img-fit mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                        class="d-block fw-600 fs-15">{{ $method->heading }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endforeach

                                            @foreach (\App\Models\ManualPaymentMethod::all() as $method)
                                                <div id="manual_payment_info_{{ $method->id }}" class="d-none">
                                                    @php echo $method->description @endphp
                                                    @if ($method->bank_info != null)
                                                        <ul>
                                                            @foreach (json_decode($method->bank_info) as $key => $info)
                                                                <li>{{ translate('Bank Name') }} -
                                                                    {{ $info->bank_name }},
                                                                    {{ translate('Account Name') }} -
                                                                    {{ $info->account_name }},
                                                                    {{ translate('Account Number') }} -
                                                                    {{ $info->account_number }},
                                                                    {{ translate('Routing Number') }} -
                                                                    {{ $info->routing_number }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>

                                <!-- Offline Payment Fields -->
                                @if (addon_is_activated('offline_payment'))
                                    <div class="d-none mb-3 rounded border bg-white p-3 text-left">
                                        <div id="manual_payment_description">

                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>{{ translate('Transaction ID') }} <span
                                                            class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control mb-3" name="trx_id"
                                                       id="trx_id" placeholder="{{ translate('Transaction ID') }}"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label">{{ translate('Photo') }}</label>
                                            <div class="col-md-9">
                                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                            {{ translate('Browse') }}</div>
                                                    </div>
                                                    <div class="form-control file-amount">{{ translate('Choose image') }}
                                                    </div>
                                                    <input type="hidden" name="photo" class="selected-files">
                                                </div>
                                                <div class="file-preview box sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="py-4 px-4 text-center bg-soft-warning mt-4 visa_master" style="display: none;background-color: #f6f6f6 !important; min-width:300px;max-width:480px;border-radius:5px;box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;" >

                                    <div class="col mt-2">
                                        <input type="text" class="form-control" placeholder="Cardholder Name"
                                               id="cardholder-name" readonly>
                                        <span class="text-danger card-holder-error err"></span>
                                    </div>
                                    <div class="col mt-2">
                                        <input type="text" class="form-control" placeholder="Card Number"
                                               id="card-number" readonly>
                                        <span class="text-danger card-number-error err"></span>
                                    </div>
                                    <div class="col mt-2">
                                        <div class="row">
                                            <div class="form-group col-md-4
">
                                                <input type="text" class="form-control" placeholder="MM"
                                                       id="expiry-month" readonly>
                                                <span class="text-danger exp-month-error err"></span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <input type="text" class="form-control" placeholder="YYYY"
                                                       id="expiry-year" readonly>
                                                <span class="text-danger exp-year-error err"></span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <input type="text" class="form-control" placeholder="CVC"
                                                       id="security-code" readonly>
                                                <span class="text-danger cvv-error err"></span>
                                            </div>
                                            <div class="fs-14 mb-3">
                                                <img style="max-width: 100px;  margin-left:0px;"src="{{asset('public/visa_master.png')}}">
                                                <span class="opacity-80"></span>
                                                <span class="fw-700"></span>

                                            </div>
                                            @php
                                                $subtotal = 0;
                                                $tax = 0;
                                                $shipping = 0;
                                                $product_shipping_cost = 0;
                                                $shipping_region = $shipping_info['city'];
                                            @endphp
                                            @foreach ($carts as $key => $cartItem)
                                                @php
                                                    $product = \App\Models\Product::find($cartItem['product_id']);
                                                    $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                                                    $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                                                    $product_shipping_cost = $cartItem['shipping_cost'];

                                                    $shipping += $product_shipping_cost;

                                                    $product_name_with_choice = $product->getTranslation('name');
                                                    if ($cartItem['variant'] != null) {
                                                        $product_name_with_choice = $product->getTranslation('name') . ' - ' . $cartItem['variant'];
                                                    }
                                                @endphp
                                            @endforeach
                                            <input type="hidden" id="sub_total" value="{{ $subtotal }}">
                                        </div>

                                    </div>
                                    <div class="col-lg-2">

                                    </div>


                                </div>

                                <div class="py-4 px-4 text-center bg-soft-warning mt-4 other_payment_option" style="display: none;background-color: #f6f6f6 !important; min-width:250px;max-width:400px;border-radius:5px;box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;" >

                                    <img src="{{asset('public/otherpayment.png')}}" style="max-width: 320px">

                                </div>
                                <!-- Wallet Payment -->
                                @if (Auth::check() && get_setting('wallet_system') == 1)
                                    {{-- <div class="py-4 px-4 text-center bg-soft-warning mt-4">
                                         <div class="fs-14 mb-3">
                                             <span class="opacity-80">{{ translate('Or, Your wallet balance :') }}</span>
                                             <span class="fw-700">{{ single_price(Auth::user()->balance) }}</span>
                                         </div>
                                         @if (Auth::user()->balance < $total)
                                             <button type="button" class="btn btn-secondary" disabled>
                                                 {{ translate('Insufficient balance') }}
                                             </button>
                                         @else
                                             <button type="button" onclick="use_wallet()"
                                                     class="btn btn-primary fs-14 fw-700 px-5 rounded-0">
                                                 {{ translate('Pay with wallet') }}
                                             </button>
                                         @endif
                                     </div>--}}
                                @endif


                            </div>

                            <!-- Agree Box -->
                            <div class="pt-3 px-4 fs-14">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" required id="agree_checkbox">
                                    <span class="aiz-square-check"></span>
                                    <span>{{ translate('I agree to the') }}</span>
                                </label>
                                <a href="{{ route('terms') }}"
                                   class="fw-700">{{ translate('terms and conditions') }}</a>,
                                <a href="{{ route('returnpolicy') }}"
                                   class="fw-700">{{ translate('return policy') }}</a> &
                                <a href="{{ route('privacypolicy') }}"
                                   class="fw-700">{{ translate('privacy policy') }}</a>
                            </div>

                            <div class="row align-items-center pt-3 px-4 mb-4">
                                <!-- Return to shop -->
                                <div class="col-6">
                                    <a href="{{ route('home') }}" class="btn btn-link fs-14 fw-700 px-0">
                                        <i class="las la-arrow-left fs-16"></i>
                                        {{ translate('Return to shop') }}
                                    </a>
                                </div>
                                <!-- Complete Ordert -->
                                <div class="col-6 text-right">
                                    <button type="button"
                                            class="btn btn-primary fs-14 fw-700 rounded-0 px-4 complete_order">{{ translate('Complete Order') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Cart Summary -->
                <div class="col-lg-4 mt-lg-0 mt-4" id="cart_summary">
                    @include('frontend.partials.cart_summary')
                </div>
            </div>
        </div>
    </section>
    <form id="redirect_route" action="{{route('order_confirmed')}}" method="get">

    </form>
    <div class="modal activation-modal mpgs_modal" id="payment-modal" tabindex="-1" role="dialog"
         aria-labelledby="payment-modal-label" aria-hidden="true" style="">
        <div class="modal-dialog modal-dialog-centered activation-modal-dialog" role="document">
            <div class="modal-content">
                <!--
                        <div class="modal-header">

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                -->
                <div class="modal-body">
                    <div class="container-fluid" id="notify">
                        <iframe id="juk_juk">

                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal activation-modal payment-fail-modal" id="payment-fail-modal" tabindex="-1" role="dialog"
         aria-labelledby="payment-fail-modal-label" aria-hidden="true" style="">
        <div class="modal-dialog modal-dialog-centered activation-modal-dialog" role="document">
            <div class="modal-content">
                <!--
                        <div class="modal-header">

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                -->
                <div class="modal-body">
                    <div class="container-fluid" id="notify">
                        <div class="container-fluid conformation-outer">
                            <div class="row row_clr">
                                <div class="col-12 conformation-inner">
                                    <div class="row row_clr conformation-inner-top">

                                        <h3>Sorry</h3>
                                    </div>
                                    <div class="row row_clr conformation-inner-middle">
                                        <div class="col-12 inner">
                                            <h4 class="status">
                                                Purchase Failed Please Try Again
                                            </h4>

                                        </div>
                                    </div>
                                    <div class="row row_clr conformation-inner-bottom">
                                        <div class="col-12 inner">

                                            <a href="#" class="go" id="shut-down">Go to Payment</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/require.js/2.3.6/require.min.js"></script>
    <!-- <script src="{{asset('js/env.js')}}"></script> -->
    <!-- PREVENT CLICK-JACKING -->
    <style id="antiClickjack">
        body {
            display: none !important;
        }
    </style>
    <script>
        if (self === top) {
            var antiClickjack = document.getElementById("antiClickjack");
            antiClickjack.parentNode.removeChild(antiClickjack);
        } else {
            top.location = self.location;
        }
    </script>


    <script src="{{ static_asset('js/webxpay.hostedsession.js') }}"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {


            $(".online_payment").click(function () {
                $('#manual_payment_description').parent().addClass('d-none');
                if ($(this).val() == "online") {
                        $('.visa_master').fadeIn();
                        $('.other_payment_option').fadeOut();
                        $('.complete_order').text("Pay Now");
                } else {
                    $('.visa_master').fadeOut();
                    $('.other_payment_option').fadeOut();
                    $('.complete_order').text("Complete order");
                    
                }
            });

            $(".online_payment_other").click(function () {

                if ($(this).val() == "webxpay") {
                    $('.other_payment_option').fadeIn();
                    $('.visa_master').fadeOut();
                    $('.complete_order').text("Pay Now");
                } else {
                    $('.other_payment_option').fadeOut();
                    $('.visa_master').fadeOut();
                    $('.complete_order').text("Complete order");
                }
            });


            toggleManualPaymentData($('input[name=payment_option]:checked').data('id'));
        });

        var minimum_order_amount_check ={{ get_setting('minimum_order_amount_check') == 1 ? 1 : 0 }};
        var minimum_order_amount ={{ get_setting('minimum_order_amount_check') == 1 ? get_setting('minimum_order_amount') : 0 }};

        function use_wallet() {
            $('input[name=payment_option]').val('wallet');
            if ($('#agree_checkbox').is(":checked")) {
                ;
                if (minimum_order_amount_check && $('#sub_total').val() < minimum_order_amount) {
                    AIZ.plugins.notify('danger',
                        '{{ translate('You order amount is less then the minimum order amount') }}');
                } else {
                    $('#checkout-form').submit();
                }
            } else {
                AIZ.plugins.notify('danger', '{{ translate('You need to agree with our policies') }}');
            }
        }

        var url = "{{url('/')}}";
        var token_mid = "{{Config::get('key_values.commercial_mpgs_mid')}}";
        var type = '';


        WebxpayTokenizeInit({
            card: {
                number: "#card-number",
                securityCode: "#security-code",
                expiryMonth: "#expiry-month",
                expiryYear: "#expiry-year",
                nameOnCard: "#cardholder-name",
            },
            ready: afterInit,
            credentials: {
                id: token_mid,
                version: '63'
            }
        });


        function afterInit(GenerateSession) {
            $('.complete_order').click(function () {
                var checkedValue = $('.online_payment:checked').val();


                $(this).prop('disabled', true);
                if ($('#agree_checkbox').is(":checked")) {
                    if (minimum_order_amount_check && $('#sub_total').val() < minimum_order_amount) {
                        AIZ.plugins.notify('danger',
                            '{{ translate('You order amount is less then the minimum order amount') }}');
                    } else {

                        var offline_payment_active = '{{ addon_is_activated('offline_payment') }}';
                        if (offline_payment_active == '1' && $('.offline_payment_option').is(":checked") && $('#trx_id').val() == '') {
                            AIZ.plugins.notify('danger', '{{ translate('You need to put Transaction id') }}');
                            $(this).prop('disabled', false);
                        } else {

                            if (checkedValue == "online") {

                                GenerateSession(
                                    function (session) {
                                        $(this).removeAttr("disabled");
                                        // window.location.href = "?sessionpay3ds=" + session;

                                        $.ajax({
                                            url: "{{route('checkout.session_pay')}}",
                                            method: "POST",
                                            data: {
                                                "_token": "{{ csrf_token() }}",
                                                "session_id": session,

                                            },
                                            dataType: "json",
                                            success: function (data) {
                                                if (data.success_proceed == true) {
                                                    $('#payment-modal').hide();

                                                    $('form#redirect_route').submit();


                                                } else {

                                                    $("#juk_juk").attr("srcdoc", data.response.html3ds);
                                                    $('#payment-modal').show();
                                                    if (data.response.html3ds !== "") {
                                                        console.log(data)

                                                        var id = setInterval(function () {
                                                            $.ajax({
                                                                url: "{{url('get-payment-status')}}",
                                                                type: 'POST',
                                                                data: {
                                                                    "_token": "{{ csrf_token() }}",
                                                                    "order_id": data.order_id
                                                                },
                                                                cache: false,
                                                                dataType: 'json',
                                                                processData: false, // Don't process the files
                                                                contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                                                                success: function (data) {


                                                                    $(".Loader").hide();
                                                                    if (data.payment_status == false) {

                                                                        $('#payment-modal').hide();
                                                                        $('#payment-fail-modal').show();

                                                                        clearInterval(id);

                                                                    } else {
                                                                        clearInterval(id);
                                                                        $('#payment-modal').hide();

                                                                        $('form#redirect_route').submit();
                                                                    }
                                                                },
                                                                error: function (data) {
                                                                }
                                                            });
                                                        }, 15000); // 30 seconds
                                                    }
                                                }
                                            }
                                        });
                                    },
                                    function (error) {
                                        handleErrors(error);
                                    }
                                );
                            }else{
                                $('#checkout-form').submit();
                            }

                        }
                    }
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('You need to agree with our policies') }}');
                    $(this).prop('disabled', false);
                }


            });

        }


        $('#shut-down').on('click', function () {
            $('#payment-fail-modal').hide();
        });

        function handleErrors(error) {
            $('button').removeAttr('disabled');

            $('.err').html('');
            $('.general-error').html('');

            switch (error.type) {
                case 'fields_in_error': {
                    if (error.details.cardNumber) {
                        if (error.details.cardNumber == 'missing') {
                            $('.card-number-error').html('Enter valid card number');
                        }
                        if (error.details.cardNumber == 'invalid') {
                            $('.card-number-error').html('Invalid card number');
                        }
                    }
                    if (error.details.expiryMonth) {
                        if (error.details.expiryMonth == 'missing') {
                            $('.exp-month-error').html('Enter expiration month');
                        }
                        if (error.details.expiryMonth == 'invalid') {
                            $('.exp-month-error').html('Invalid expiration month');
                        }
                    }
                    if (error.details.expiryYear) {
                        if (error.details.expiryYear == 'missing') {
                            $('.exp-year-error').html('Enter expiration year');
                        }
                        if (error.details.expiryYear == 'invalid') {
                            $('.exp-month-error').html('Invalid expiration year');
                        }
                    }
                    if (error.details.securityCode) {
                        if (error.details.securityCode == 'missing') {
                            $('.cvv-error').html('Enter CVV');
                        }
                        if (error.details.securityCode == 'invalid') {
                            $('.cvv-error').html('Invalid CVV');
                        }
                    }
                    console.error('missing card details', error.details);
                    break;
                }
                case 'request_timeout': {
                    $('.general-error').html('<span class="text-decoration-uppercase">' + error.details + '</span>')
                    console.error('request time out', error.details);
                    break;
                }
                case 'system_error': {
                    if (error.details == 'cvv missing') {
                        $('.general-error').html('Enter CVV details');
                    } else {
                        $('.general-error').html(error.details);
                    }
                    console.error('system error', error.details);
                    break;
                }
            }
        }

        function toggleManualPaymentData(id) {
            if (typeof id != 'undefined') {
                $('#manual_payment_description').parent().removeClass('d-none');
                $('#manual_payment_description').html($('#manual_payment_info_' + id).html());
            }
        }

        $(document).on("click", "#coupon-apply", function () {
            var data = new FormData($('#apply-coupon-form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: "{{ route('checkout.apply_coupon_code') }}",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data, textStatus, jqXHR) {
                    AIZ.plugins.notify(data.response_message.response, data.response_message.message);
                    $("#cart_summary").html(data.html);
                }
            })
        });

        $(document).on("click", "#coupon-remove", function () {
            var data = new FormData($('#remove-coupon-form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: "{{ route('checkout.remove_coupon_code') }}",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data, textStatus, jqXHR) {
                    $("#cart_summary").html(data);
                }
            })
        })
    </script>
@endsection
