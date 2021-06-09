@include('front.theme.header')
<section class="cart">
    <div class="container">
        <h2 class="sec-head">Giỏ hàng của tôi</h2>
        <div class="row">
            @if (count($cartdata) == 0)
            <p>Không tìm thấy đơn hàng nào</p>
            @else
            <div class="col-lg-8">
                @foreach ($cartdata as $cart)
                <?php
                        $data[] = array(
                            "total_price" => $cart->price
                        );
                    ?>
                <div class="cart-box">
                    <div class="cart-pro-img">
                        <img src='{{$cart["itemimage"]->image}}' alt="">
                    </div>
                    <div class="cart-pro-details">
                        <div class="cart-pro-edit">
                            <a class="cart-pro-name">{{$cart->item_name}}</a>
                            <a href="javascript:void(0)"><i class="fal fa-trash-alt" onclick="RemoveCart('{{$cart->id}}')"></i></a>
                        </div>
                        <div class="cart-pro-edit">
                            <input type="hidden" name="max_qty" id="max_qty" value="{{$getdata->max_order_qty}}">
                            <div class="pro-add">
                                <div class="value-button sub" id="decrease" onclick="qtyupdate('{{$cart->id}}','{{$cart->item_id}}','decreaseValue')" value="Decrease Value">
                                    <i class="fal fa-minus-circle"></i>
                                </div>
                                <input type="number" id="number_{{$cart->id}}" name="number" value="{{$cart->qty}}" readonly="" min="1" max="10" style="background-color: #f4f4f8;" />
                                <div class="value-button add" id="increase" onclick="qtyupdate('{{$cart->id}}','{{$cart->item_id}}','increase')" value="Increase Value">
                                    <i class="fal fa-plus-circle"></i>
                                </div>
                            </div>
                            <p class="cart-pricing">{{number_format($cart->price)}}{{$taxval->currency}}</p>
                        </div>
                        @if (count($cart['addons']) != 0)
                        <div class="cart-addons-wrap">
                            @foreach ($cart['addons'] as $addons)
                            <div class="cart-addons">
                                <b>{{$addons['name']}}</b> : {{number_format($addons['price'])}}{{$taxval->currency}}
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @if ($cart->item_notes != "")
                        <textarea placeholder="Your product message" readonly="">{{$cart->item_notes}}</textarea>
                        @endif
                    </div>
                </div>
                @endforeach
                @if (Session::has('offer_amount'))
                <div class="promo-code">
                    <form>
                        <div class="promo-wrap">
                            <input type="text" name="removepromocode" id="removepromocode" autocomplete="off" readonly="" value="{{Session::get('offer_code')}}">
                            <button class="btn" id="ajaxRemove">Xóa</button>
                        </div>
                    </form>
                </div>
                @else
                <div class="promo-code">
                    <form>
                        <div class="promo-wrap">
                            <input type="text" placeholder="Áp dụng mã giảm giá" name="promocode" id="promocode" autocomplete="off" readonly="">
                            <button class="btn" id="ajaxSubmit">Áp dụng</button>
                        </div>
                    </form>
                    <p data-toggle="modal" data-target="#staticBackdrop">Chọn mã giảm giá</p>
                </div>
                @endif
            </div>
            <div class="col-lg-4">
                <?php 
                    $order_total = array_sum(array_column(@$data, 'total_price'));
                    $taxprice = array_sum(array_column(@$data, 'total_price'))*$taxval->tax/100; 
                    $total = array_sum(array_column(@$data, 'total_price'))+$taxprice+$taxval->delivery_charge;
                    ?>
                <div class="cart-summary">
                    <h2 class="sec-head" style="font-size: 34px;">Chi tiết thanh toán</h2>
                    <p class="pro-total">Phí đơn hàng <span>{{number_format($order_total)}}{{$taxval->currency}}</span></p>
                    <p class="pro-total">Thuế({{$taxval->tax}}%) <span>{{number_format($taxprice)}}{{$taxval->currency}}</span></p>
                    <p class="pro-total" id="delivery_charge_hide">Phí vận chuyển<span>{{number_format($taxval->delivery_charge)}}{{$taxval->currency}}</span></p>
                    @if (Session::has('offer_amount'))
                    <p class="pro-total offer_amount">Khuyến mãi ({{Session::get('offer_code')}})</span>
                        <span id="offer_amount">
                            -{{$taxval->currency}}{{number_format($order_total*Session::get('offer_amount')/100)}}
                        </span>
                    </p>
                    @else
                    <p class="pro-total offer_amount" style="display: none">Khuyến mãi <span id="offer_amount"></span></p>
                    @endif
                    @if (Session::has('offer_amount'))
                    <p class="cart-total">Tổng cộng
                        <span id="total_amount">
                            {{number_format($order_total+$taxval->delivery_charge+$taxprice-$order_total*Session::get('offer_amount')/100)}}{{$taxval->currency}}
                        </span>
                    </p>
                    @else
                    <p class="cart-total">Tổng cộng <span id="total_amount">{{number_format($total)}}{{$taxval->currency}}</span></p>
                    @endif
                    <h4 class="sec-head openmsg mt-5" style="color: red; display: none;">Cửa hàng đã đóng.</h4>
                    <div class="cart-delivery-type open">
                        <label for="cart-delivery">
                            <input type="radio" name="cart-delivery" id="cart-delivery" checked value="1">
                            <div class="cart-delivery-type-box">
                                <img src="{!! asset('front/images/pickup-truck.png') !!}" height="40" width="40" alt="">
                                <p>Ship hàng</p>
                            </div>
                        </label>
                        <label for="cart-pickup">
                            <input type="radio" name="cart-delivery" id="cart-pickup" value="2">
                            <div class="cart-delivery-type-box">
                                <img src="{!! asset('front/images/delivery.png') !!}" height="40" width="40" alt="">
                                <p>Tự lấy hàng</p>
                            </div>
                        </label>
                    </div>
                    @if (env('Environment') == 'sendbox')
                    <div class="promo-wrap open">
                        <input type="text" placeholder="Nhập địa chỉ giao hàng" name="address" size="50" id="address" value="" required="" readonly="" autocomplete="on">
                        <input type="hidden" id="lat" name="lat" value="10.845839903844823" />
                        <input type="hidden" id="lang" name="lang" value="106.79454379666092" />
                        <input type="hidden" id="city" name="city" placeholder="city" value="TP.HCM" />
                        <input type="hidden" id="state" name="state" placeholder="state" value="Quận 9" />
                        <input type="hidden" id="country" name="country" placeholder="country" value="Viet nam" />
                    </div>
                    <div class="promo-wrap open">
                        <input type="text" id="postal_code" name="postal_code" placeholder="Mã vùng(VD:70000(TP.HCM))" value="" readonly="" />
                    </div>
                    <div class="promo-wrap open">
                        <input type="text" placeholder="Door / Flat no." name="building" id="building" required="" value="" readonly="">
                    </div>
                    <div class="promo-wrap open">
                        <input type="text" placeholder="Landmark" name="landmark" id="landmark" required="" value="" readonly="">
                    </div>
                    @else
                    <div class="promo-wrap open">
                        <input type="text" placeholder="Địa chỉ giao hàng" name="address" size="50" id="address" required="" autocomplete="on">
                        <input type="hidden" id="lat" name="lat" />
                        <input type="hidden" id="lang" name="lang" />
                        <input type="hidden" id="city" name="city" placeholder="city" />
                        <input type="hidden" id="state" name="state" placeholder="state" />
                        <input type="hidden" id="country" name="country" placeholder="country" />
                    </div>
                    <div class="promo-wrap open">
                        <input type="text" id="postal_code" name="postal_code" placeholder="Mã vùng" />
                    </div>
                    <div class="promo-wrap open">
                        <input type="text" placeholder="Số nhà..." name="building" id="building" required="">
                    </div>
                    <div class="promo-wrap open">
                        <input type="text" placeholder="Tên địa điểm(VD:Ký túc xá ĐHGTVT)" name="landmark" id="landmark" required="">
                    </div>
                    @endif
                    <div class="promo-wrap open">
                        <textarea name="notes" id="notes" placeholder="Nhập ghi chú..." rows="3"></textarea>
                    </div>
                    <input type="hidden" name="order_total" id="order_total" value="{{$order_total}}">
                    <input type="hidden" name="tax" id="tax" value="{{$taxval->tax}}">
                    <input type="hidden" name="tax_amount" id="tax_amount" value="{{$taxprice}}">
                    <input type="hidden" name="email" id="email" value="{{Session::get('email')}}">
                    <input type="hidden" name="delivery_charge" id="delivery_charge" value="{{$taxval->delivery_charge}}">
                    @if (Session::has('offer_amount'))
                    <input type="hidden" name="discount_amount" id="discount_amount" value="{{$order_total*Session::get('offer_amount')/100}}">
                    @else
                    <input type="hidden" name="discount_amount" id="discount_amount" value="">
                    @endif
                    @if (Session::has('offer_amount'))
                    <input type="hidden" name="paid_amount" id="paid_amount" value="{{$order_total+$taxval->delivery_charge+$taxprice-$order_total*Session::get('offer_amount')/100}}">
                    @else
                    <input type="hidden" name="paid_amount" id="paid_amount" value="{{$total}}">
                    @endif
                    @if (Session::has('offer_amount'))
                    <input type="hidden" name="discount_pr" id="discount_pr" value="{{Session::get('offer_amount')}}">
                    @else
                    <input type="hidden" name="discount_pr" id="discount_pr" value="">
                    @endif
                    @if (Session::has('offer_amount'))
                    <input type="hidden" name="getpromo" id="getpromo" value="{{Session::get('offer_code')}}">
                    @else
                    <input type="hidden" name="getpromo" id="getpromo" value="">
                    @endif
                    <div class="mt-3">
                        <button type="button" style="width: 100%;" class="btn open comman" onclick="WalletOrder()">Ví của tôi ({{number_format($userinfo->wallet)}}{{$taxval->currency}})</button>
                    </div>
                    @foreach($getpaymentdata as $paymentdata)
                    @if ($paymentdata->payment_name == "COD")
                    <div class="mt-3">
                        <button type="button" style="width: 100%;" class="btn open comman" onclick="CashonDelivery()">Thanh toán khi nhận hàng</button>
                    </div>
                    @endif
                    @if ($paymentdata->payment_name == "RazorPay")
                    <div class="mt-3">
                        <button type="button" style="width: 100%;" class="btn buy_now open comman">RazorPay Payment</button>
                    </div>
                    @if($paymentdata->environment=='1')
                    <input type="hidden" name="razorpay" id="razorpay" value="{{$paymentdata->test_public_key}}">
                    @else
                    <input type="hidden" name="razorpay" id="razorpay" value="{{$paymentdata->live_public_key}}">
                    @endif
                    @endif
                    @if ($paymentdata->payment_name == "Stripe")
                    <div class="mt-3">
                        <button id="customButton" class="btn comman" style="display: none; width: 100%;">Stripe Payment</button>
                        <button class="btn open stripe comman" style="width: 100%;" onclick="stripe()">Stripe Payment</button>
                    </div>
                    @if($paymentdata->environment=='1')
                    <input type="hidden" name="stripe" id="stripe" value="{{$paymentdata->test_public_key}}">
                    @else
                    <input type="hidden" name="stripe" id="stripe" value="{{$paymentdata->live_public_key}}">
                    @endif
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
<!-- Modal -->
<div class="promo-modal modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-head">
                <h4>Chọn mã giảm giá</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @foreach ($getpromocode as $promocode)
                <div class="promo-box">
                    <button class="btn btn-copy" data-id="{{$promocode->offer_code}}">Chọn</button>
                    <p class="promo-title">{{$promocode->offer_name}}</p>
                    <p class="promo-code-here">Code :: <span>{{$promocode->offer_code}}</span></p>
                    <small>{{$promocode->description}}</small>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@include('front.theme.footer')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://checkout.stripe.com/v2/checkout.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{$taxval->map}}&libraries=places"></script>
<script type="text/javascript">
var handler = StripeCheckout.configure({
    key: $('#stripe').val(),
    image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
    locale: 'auto',
    token: function(token) {
        // You can access the token ID with `token.id`.
        // Get the token ID to your server-side code for use.

        var order_total = parseFloat($('#order_total').val());
        var tax = parseFloat($('#tax').val());
        var delivery_charge = parseFloat($('#delivery_charge').val());
        var discount_amount = parseFloat($('#discount_amount').val());
        var paid_amount = parseFloat($('#paid_amount').val());
        var notes = $('#notes').val();
        var address = $('#address').val();
        var promocode = $('#getpromo').val();
        var tax_amount = $('#tax_amount').val();
        var discount_pr = $('#discount_pr').val();
        var lat = $('#lat').val();
        var lang = $('#lang').val();
        var building = $('#building').val();
        var landmark = $('#landmark').val();
        var postal_code = $('#postal_code').val();
        var city = $('#city').val();
        var state = $('#state').val();
        var country = $('#country').val();
        var order_type = $("input:radio[name=cart-delivery]:checked").val();
        var token = token.id;


        $('#preloader').show();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ URL::to('stripe-payment/charge') }}",
            data: {
                order_total: paid_amount,
                address: address,
                promocode: promocode,
                discount_amount: discount_amount,
                discount_pr: discount_pr,
                tax: tax,
                tax_amount: tax_amount,
                delivery_charge: delivery_charge,
                notes: notes,
                order_type: order_type,
                lat: lat,
                lang: lang,
                building: building,
                landmark: landmark,
                postal_code: postal_code,
                city: city,
                state: state,
                country: country,
                stripeToken: token,
            },
            method: 'POST',
            success: function(response) {
                $('#preloader').hide();
                if (response.status == 1) {
                    window.location.href = 'http://localhost/laravel/foodorder/public/orders';
                } else {
                    $('#ermsg').text(response.message);
                    $('#error-msg').addClass('alert-danger');
                    $('#error-msg').css("display", "block");

                    setTimeout(function() {
                        $("#error-msg").hide();
                    }, 5000);
                }
            },
            error: function(error) {

                // $('#errormsg').show();
            }
        });
    },
    opened: function() {
        // console.log("Form opened");
    },
    closed: function() {
        // console.log("Form closed");
    }
});

$('#customButton').on('click', function(e) {
    // Open Checkout with further options:
    var paid_amount = parseFloat($('#paid_amount').val());
    var order_total = parseFloat($('#order_total').val());
    var order_type = $("input:radio[name=cart-delivery]:checked").val();
    var address = $('#address').val();
    var lat = $('#lat').val();
    var lang = $('#lang').val();
    var landmark = $('#landmark').val();
    var postal_code = $('#postal_code').val();
    var building = $('#building').val();
    var email = $('#email').val();

    if (order_type == "1") {
        if (address == "" && lat == "" && lang == "") {
            $('#ermsg').text('Bạn chưa nhập địa chỉ');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display", "block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);
        } else if (lat == "") {
            $('#ermsg').text('Hãy chọn 1 địa chỉ từ gợi ý');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display", "block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);

        } else if (lang == "") {
            $('#ermsg').text('Hãy chọn 1 địa chỉ từ gợi ý');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display", "block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);

        } else if (building == "") {
            $('#ermsg').text('Bạn chưa nhập số nhà');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display", "block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);

        } else if (landmark == "") {
            $('#ermsg').text('Bạn chưa nhập địa điểm');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display", "block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);
        } else if (postal_code == "") {
            $('#ermsg').text('Bạn chưa nhập mã vùng');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display", "block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);
        } else {
            handler.open({
                name: 'Food App',
                description: 'Food Service',
                amount: paid_amount * 100,
                email: email
            });
            e.preventDefault();
            // Close Checkout on page navigation:
            $(window).on('popstate', function() {
                handler.close();
            });
        }
    } else {

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ URL::to('/home/checkpincode') }}",
            data: {
                postal_code: postal_code,
                order_total: order_total,
            },
            method: 'POST',
            success: function(result) {
                if (result.status == 1) {
                    handler.open({
                        name: 'Food App',
                        description: 'Food Service',
                        amount: paid_amount * 100,
                        email: email
                    });
                    e.preventDefault();
                    // Close Checkout on page navigation:
                    $(window).on('popstate', function() {
                        handler.close();
                    });
                } else {
                    $('#ermsg').text(result.message);
                    $('#error-msg').addClass('alert-danger');
                    $('#error-msg').css("display", "block");

                    setTimeout(function() {
                        $("#error-msg").hide();
                    }, 5000);
                }
            },
        });
    }

});

</script>
@if (env('Environment') != 'sendbox')
<script>
function initialize() {
    var input = document.getElementById('address');
    var autocomplete = new google.maps.places.Autocomplete(input);
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();

        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];

            if (addressType == "administrative_area_level_1") {
                document.getElementById("state").value = place.address_components[i].short_name;
            }

            if (addressType == "postal_code") {
                document.getElementById("postal_code").value = place.address_components[i].short_name;
            }

            if (addressType == "locality") {
                document.getElementById("city").value = place.address_components[i].short_name;
            }

            // for the country, get the country code (the "short name") also
            if (addressType == "country") {
                document.getElementById("country").value = place.address_components[i].short_name;
            }
        }

        document.getElementById('lat').value = place.geometry.location.lat();
        document.getElementById('lang').value = place.geometry.location.lng();
    });
}
google.maps.event.addDomListener(window, 'load', initialize);

</script>
@endif
<script>
$(document).ready(function() {
    $("input[name='cart-delivery']").click(function() {
        var test = $(this).val();

        if (test == 1) {
            $("#address").show();
            $("#delivery_charge_hide").show();
            $("#building").show();
            $("#landmark").show();
            $("#postal_code").show();
            $(".stripe").show();
            $("#dummy-msg").show();
            $("#customButton").hide();

            var order_total = parseFloat($('#order_total').val());
            var delivery_charge = parseFloat($('#delivery_charge').val());
            var tax_amount = parseFloat($('#tax_amount').val());
            var discount_amount = parseFloat($('#discount_amount').val());

            if (isNaN(discount_amount)) {
                $('#total_amount').text(((order_total + tax_amount + delivery_charge) + '{{$taxval->currency}}').replace(/\B(?=(\d{3})+(?!\d))/g, ","));

                $('#paid_amount').val((order_total + tax_amount + delivery_charge).toFixed(2));
            } else {
                $('#total_amount').text(((order_total + tax_amount + delivery_charge - discount_amount) + '{{$taxval->currency}}').replace(/\B(?=(\d{3})+(?!\d))/g, ","));

                $('#paid_amount').val((order_total + tax_amount + delivery_charge - discount_amount).toFixed(2));
            }

        } else {
            $("#address").hide();
            $("#delivery_charge_hide").hide();
            $("#building").hide();
            $("#landmark").hide();
            $("#postal_code").hide();
            $("#dummy-msg").hide();
            $(".stripe").hide();
            $("#customButton").show();

            var order_total = parseFloat($('#order_total').val());
            var delivery_charge = parseFloat($('#delivery_charge').val());
            var tax_amount = parseFloat($('#tax_amount').val());
            var discount_amount = parseFloat($('#discount_amount').val());

            if (isNaN(discount_amount)) {
                $('#total_amount').text(((order_total + tax_amount) + '{{$taxval->currency}}').replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $('#paid_amount').val((order_total + tax_amount));
            } else {
                $('#total_amount').text(((order_total + tax_amount - discount_amount) + '{{$taxval->currency}}').replace(/\B(?=(\d{3})+(?!\d))/g, ","));

                $('#paid_amount').val((order_total + tax_amount - discount_amount));
            }
        }
    });
});


// var SITEURL = '{{URL::to('
// ')}}';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

    }
});
$('body').on('click', '.buy_now', function(e) {
    var order_total = parseFloat($('#order_total').val());
    var tax = parseFloat($('#tax').val());
    var delivery_charge = parseFloat($('#delivery_charge').val());
    var discount_amount = parseFloat($('#discount_amount').val());
    var paid_amount = parseFloat($('#paid_amount').val());
    var notes = $('#notes').val();
    var address = $('#address').val();
    var promocode = $('#getpromo').val();
    var tax_amount = $('#tax_amount').val();
    var discount_pr = $('#discount_pr').val();
    var lat = $('#lat').val();
    var lang = $('#lang').val();
    var building = $('#building').val();
    var landmark = $('#landmark').val();
    var postal_code = $('#postal_code').val();
    var order_type = $("input:radio[name=cart-delivery]:checked").val();

    if (order_type == "1") {
        if (address == "" && lat == "" && lang == "") {
            $('#ermsg').text('Bạn chưa nhập địa chỉ');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display", "block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);
        } else if (lat == "") {
            $('#ermsg').text('Hãy chọn 1 địa chỉ từ gợi ý');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display", "block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);

        } else if (lang == "") {
            $('#ermsg').text('Hãy chọn 1 địa chỉ từ gợi ý');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display", "block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);

        } else if (building == "") {
            $('#ermsg').text('Bạn chưa nhập số nhà');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display", "block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);

        } else if (landmark == "") {
            $('#ermsg').text('Bạn chưa nhập địa điểm');
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display", "block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);
        } else {

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ URL::to('/home/checkpincode') }}",
                data: {
                    postal_code: postal_code,
                    order_total: order_total,
                },
                method: 'POST',
                success: function(result) {
                    if (result.status == 1) {
                        var options = {
                            "key": $('#razorpay').val(),
                            "amount": (parseInt(paid_amount * 100)), // 2000 paise = INR 20
                            "name": "Food App",
                            "description": "Order Value",
                            "image": "{!! asset('front/images/logo.png') !!}",
                            "handler": function(response) {
                                $('#preloader').show();
                                $.ajax({
                                    url: 'http://localhost/laravel/foodorder/public/payment',
                                    type: 'post',
                                    dataType: 'json',
                                    data: {
                                        order_total: paid_amount,
                                        razorpay_payment_id: response.razorpay_payment_id,
                                        address: address,
                                        promocode: promocode,
                                        discount_amount: discount_amount,
                                        discount_pr: discount_pr,
                                        tax: tax,
                                        tax_amount: tax_amount,
                                        delivery_charge: delivery_charge,
                                        notes: notes,
                                        order_type: order_type,
                                        lat: lat,
                                        lang: lang,
                                        building: building,
                                        landmark: landmark,
                                        postal_code: postal_code,
                                    },
                                    success: function(msg) {
                                        $('#preloader').hide();
                                        window.location.href = 'http://localhost/laravel/foodorder/public/orders';
                                    }
                                });

                            },
                            "prefill": {
                                "contact": '{{@$userinfo->mobile}}',
                                "email": '{{@$userinfo->email}}',
                                "name": '{{@$userinfo->name}}',
                            },
                            "theme": {
                                "color": "#fe734c"
                            }
                        };

                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                        e.preventDefault();
                    } else {
                        $('#ermsg').text(result.message);
                        $('#error-msg').addClass('alert-danger');
                        $('#error-msg').css("display", "block");

                        setTimeout(function() {
                            $("#error-msg").hide();
                        }, 5000);
                    }
                },
            });
        }
    } else {

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ URL::to('/home/checkpincode') }}",
            data: {
                order_total: order_total,
            },
            method: 'POST',
            success: function(result) {
                if (result.status == 1) {
                    var options = {
                        "key": $('#razorpay').val(),
                        "amount": (parseInt(paid_amount * 100)), // 2000 paise = INR 20
                        "name": "Food App",
                        "description": "Order Value",
                        "image": "{!! asset('front/images/logo.png') !!}",
                        "handler": function(response) {
                            $('#preloader').show();
                            $.ajax({
                                url: 'http://localhost/laravel/foodorder/public/payment',
                                type: 'post',
                                dataType: 'json',
                                data: {
                                    order_total: paid_amount,
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    address: address,
                                    promocode: promocode,
                                    discount_amount: discount_amount,
                                    discount_pr: discount_pr,
                                    tax: tax,
                                    tax_amount: tax_amount,
                                    delivery_charge: '0.00',
                                    notes: notes,
                                    order_type: order_type,
                                    lat: lat,
                                    lang: lang,
                                    building: building,
                                    landmark: landmark,
                                    postal_code: postal_code,
                                },
                                success: function(msg) {
                                    $('#preloader').hide();
                                    window.location.href =
                                        'http://localhost/laravel/foodorder/public/orders';
                                }
                            });

                        },
                        "prefill": {
                            "contact": '{{@$userinfo->mobile}}',
                            "email": '{{@$userinfo->email}}',
                            "name": '{{@$userinfo->name}}',
                        },
                        "theme": {
                            "color": "#fe734c"
                        }
                    };

                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                    e.preventDefault();
                } else {
                    $('#ermsg').text(result.message);
                    $('#error-msg').addClass('alert-danger');
                    $('#error-msg').css("display", "block");

                    setTimeout(function() {
                        $("#error-msg").hide();
                    }, 5000);
                }
            },
        });
    }
});
/*document.getElementsClass('buy_plan1').onclick = function(e){
    rzp1.open();
    e.preventDefault();
}*/

function WalletOrder() {
    var total_order = parseFloat($('#order_total').val());
    var tax = parseFloat($('#tax').val());
    var delivery_charge = parseFloat($('#delivery_charge').val());
    var discount_amount = parseFloat($('#discount_amount').val());
    var paid_amount = parseFloat($('#paid_amount').val());
    var notes = $('#notes').val();
    var address = $('#address').val();
    var promocode = $('#getpromo').val();
    var tax_amount = $('#tax_amount').val();
    var discount_pr = $('#discount_pr').val();
    var lat = $('#lat').val();
    var lang = $('#lang').val();
    var postal_code = $('#postal_code').val();
    var building = $('#building').val();
    var landmark = $('#landmark').val();
    var order_type = $("input:radio[name=cart-delivery]:checked").val();

    $('#preloader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ URL::to('/orders/walletorder') }}",
        data: {
            order_total: paid_amount,
            total_order: total_order,
            address: address,
            promocode: promocode,
            discount_amount: discount_amount,
            discount_pr: discount_pr,
            tax: tax,
            tax_amount: tax_amount,
            delivery_charge: delivery_charge,
            notes: notes,
            order_type: order_type,
            lat: lat,
            lang: lang,
            postal_code: postal_code,
            building: building,
            landmark: landmark,
        },
        method: 'POST',
        success: function(response) {
            $('#preloader').hide();
            if (response.status == 1) {
                window.location.href = 'http://localhost/laravel/foodorder/public/orders';
            } else {
                $('#ermsg').text(response.message);
                $('#error-msg').addClass('alert-danger');
                $('#error-msg').css("display", "block");

                setTimeout(function() {
                    $("#error-msg").hide();
                }, 5000);
            }
        },
        error: function(error) {

            // $('#errormsg').show();
        }
    });
}

function CashonDelivery() {
    var total_order = parseFloat($('#order_total').val());
    var tax = parseFloat($('#tax').val());
    var delivery_charge = parseFloat($('#delivery_charge').val());
    var discount_amount = parseFloat($('#discount_amount').val());
    var paid_amount = parseFloat($('#paid_amount').val());
    var notes = $('#notes').val();
    var address = $('#address').val();
    var promocode = $('#getpromo').val();
    var tax_amount = $('#tax_amount').val();
    var discount_pr = $('#discount_pr').val();
    var lat = $('#lat').val();
    var lang = $('#lang').val();
    var postal_code = $('#postal_code').val();
    var building = $('#building').val();
    var landmark = $('#landmark').val();
    var order_type = $("input:radio[name=cart-delivery]:checked").val();
    console.log(total_order);

    $('#preloader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ URL::to('/orders/cashondelivery') }}",
        data: {
            order_total: paid_amount,
            total_order: total_order,
            address: address,
            promocode: promocode,
            discount_amount: discount_amount,
            discount_pr: discount_pr,
            tax: tax,
            tax_amount: tax_amount,
            delivery_charge: delivery_charge,
            notes: notes,
            order_type: order_type,
            lat: lat,
            lang: lang,
            postal_code: postal_code,
            building: building,
            landmark: landmark,
        },
        method: 'POST',
        success: function(response) {
            $('#preloader').hide();
            if (response.status == 1) {
                window.location.href = 'http://localhost/laravel/foodorder/public/orders';
            } else {
                $('#ermsg').text(response.message);
                $('#error-msg').addClass('alert-danger');
                $('#error-msg').css("display", "block");

                setTimeout(function() {
                    $("#error-msg").hide();
                }, 5000);
            }
        },
        error: function(error) {

            // $('#errormsg').show();
        }
    });
}

function stripe() {
    var postal_code = $('#postal_code').val();
    var order_total = $('#order_total').val();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ URL::to('/home/checkpincode') }}",
        data: {
            postal_code: postal_code,
            order_total: order_total,
        },
        method: 'POST',
        success: function(result) {
            if (result.status == 1) {
                $("#customButton").click();
            } else {
                $('#ermsg').text(result.message);
                $('#error-msg').addClass('alert-danger');
                $('#error-msg').css("display", "block");

                setTimeout(function() {
                    $("#error-msg").hide();
                }, 5000);
            }
        },
    });
}

</script>
<script>
jQuery(document).ready(function() {
    jQuery('#ajaxSubmit').click(function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        });
        $('#preloader').show();
        jQuery.ajax({
            url: "{{ URL::to('/cart/applypromocode') }}",
            method: 'post',
            data: {
                promocode: jQuery('#promocode').val()
            },
            success: function(response) {
                $('#preloader').hide();

                if (response.status == 1) {

                    $('.offer_amount').css("display", "flex");
                    var order_total = parseFloat($('#order_total').val());
                    var delivery_charge = parseFloat($('#delivery_charge').val());
                    var tax_amount = parseFloat($('#tax_amount').val());
                    var offer_amount = (order_total * response.data.offer_amount / 100);

                    $('#discount_pr').val(response.data.offer_amount);
                    $('#getpromo').val(response.data.offer_code);

                    $('#offer_amount').text('-$' + (order_total * response.data.offer_amount / 100).toFixed(2));
                    $('#discount_amount').val((order_total * response.data.offer_amount / 100));

                    $('#total_amount').text('{{$taxval->currency}}' + ((order_total + delivery_charge - offer_amount) + tax_amount).toFixed(2));

                    $('#paid_amount').val(((order_total + delivery_charge - offer_amount) + tax_amount).toFixed(2));

                    $('#msg').text(response.message);
                    $('#success-msg').addClass('alert-success');
                    $('#success-msg').css("display", "block");

                    location.reload();
                } else {
                    $('#ermsg').text(response.message);
                    $('#error-msg').addClass('alert-danger');
                    $('#error-msg').css("display", "block");

                    setTimeout(function() {
                        $("#success-msg").hide();
                    }, 5000);
                }

            }
        });
    });
});

jQuery(document).ready(function() {
    jQuery('#ajaxRemove').click(function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        });
        $('#preloader').show();
        jQuery.ajax({
            url: "{{ URL::to('/cart/removepromocode') }}",
            method: 'post',
            data: {
                promocode: jQuery('#promocode').val()
            },
            success: function(response) {

                $('#preloader').hide();
                if (response.status == 1) {
                    $('.offer_amount').css("display", "none");
                    var order_total = parseFloat($('#order_total').val());
                    var delivery_charge = parseFloat($('#delivery_charge').val());
                    var tax_amount = parseFloat($('#tax_amount').val());

                    $('#discount_pr').val('');

                    $('#discount_amount').val('');

                    $('#total_amount').text('{{$taxval->currency}}' + ((order_total + delivery_charge) + tax_amount).toFixed(2));

                    $('#paid_amount').val(((order_total + delivery_charge) + tax_amount).toFixed(2));

                    $('#msg').text(response.message);
                    $('#success-msg').addClass('alert-success');
                    $('#success-msg').css("display", "block");

                    location.reload();
                } else {

                }
            }
        });
    });
});

function qtyupdate(cart_id, item_id, type) {
    var qtys = parseInt($("#number_" + cart_id).val());
    var max_qty = $("#max_qty").val();
    var item_id = item_id;
    var cart_id = cart_id;

    if (type == "decreaseValue") {
        qty = qtys - 1;
    } else {
        qty = qtys + 1;
    }

    if (qty >= "1" && qty <= max_qty) {
        $('#preloader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ URL::to('/cart/qtyupdate') }}",
            data: {
                cart_id: cart_id,
                qty: qty,
                item_id,
                item_id,
                type,
                type
            },
            method: 'POST',
            success: function(response) {
                $('#preloader').hide();
                if (response.status == 1) {
                    location.reload();
                } else {
                    $('#ermsg').text(response.message);
                    $('#error-msg').addClass('alert-danger');
                    $('#error-msg').css("display", "block");

                    setTimeout(function() {
                        $("#success-msg").hide();
                    }, 5000);
                }
            },
            error: function(error) {

                // $('#errormsg').show();
            }
        });
    } else {

        if (qty < "1") {
            $('#ermsg').text("Số lượng không thể nhỏ hơn 1");
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display", "block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);
        } else {
            $('#ermsg').text("Số lượng không thể lớn hơn 10");
            $('#error-msg').addClass('alert-danger');
            $('#error-msg').css("display", "block");

            setTimeout(function() {
                $("#error-msg").hide();
            }, 5000);
        }
    }
}

function RemoveCart(cart_id) {
    swal({
            title: "Bạn chắc chứ?",
            text: "Bạn có muốn xóa sản phẩm này khỏi giỏ hàng?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Có",
            cancelButtonText: "Không",
            closeOnConfirm: false,
            closeOnCancel: false,
            showLoaderOnConfirm: true,
        },
        function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ URL::to('/cart/deletecartitem') }}",
                    data: {
                        cart_id: cart_id
                    },
                    method: 'POST',
                    success: function(response) {
                        if (response == 1) {
                            swal({
                                    title: "Thành công!",
                                    text: "Đã xóa sản phẩm khỏi giỏ hàng",
                                    type: "success",
                                    showCancelButton: true,
                                    confirmButtonClass: "btn-danger",
                                    confirmButtonText: "Ok",
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true,
                                },
                                function(isConfirm) {
                                    if (isConfirm) {
                                        swal.close();
                                        location.reload();
                                    }
                                });
                        } else {
                            swal("Đã hủy", "Đã có lỗi xảy ra:(", "error");
                        }
                    },
                    error: function(e) {
                        swal("Đã hủy", "Đã có lỗi xảy ra:(", "error");
                    }
                });
            } else {
                swal("Đã hủy", "", "error");
            }
        });
}

$('body').on('click', '.btn-copy', function(e) {

    var text = $(this).attr('data-id');
    // navigator.clipboard.writeText(text).then(function() {
    $('#promocode').val(text);
    $('#staticBackdrop').modal('hide');
    // }, function(err) {
    // console.error('Async: Could not copy text: ', err);
    // });

});

</script>
