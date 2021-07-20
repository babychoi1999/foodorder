@include('front.theme.header')>
<section class="order-details">
    <div class="container">
        <h2 class="sec-head">Ví của tôi</h2>
        <div class="row mt-5">
            <div class="col-lg-4">
                <div class="order-payment-summary" style="background-color: #fd3b2f">
                    <div class="col-4 mx-auto text-center">
                        <img src='{!! asset("public/front/images/ic_wallet.png") !!}' width="100px" alt="" class="text-center">
                    </div>
                    <h2 class="text-center mt-3">Số dư trong ví</h2>
                    <h1 class="text-center" style="color: #fff;"><span>{{number_format($walletamount->wallet)}}{{$getdata->currency}}</span></h1>
                </div>
                @foreach($getpaymentdata as $paymentdata)
                @if($paymentdata->payment_name == "VNPAY")
                <div class="mt-3">
                    <button type="button" data-toggle="modal" data-target="#AddMoneypay" style="width: 100%;" class="btn">Nạp tiền vào ví</button>
                </div>
                @endif
                @endforeach
            </div>
            <div class="col-lg-8">
                @foreach ($transaction_data as $orders)
                @if ($orders->transaction_type == 1)
                <div class="order-details-box">
                    <div class="wallet-details-img">
                        <img src='{!! asset("public/front/images/ic_trGreen.png") !!}' alt="" class="mt-1">
                    </div>
                    <div class="order-details-name mt-3">
                        <h3> {{$orders->order_number}} <span style="color: #000;">{{$orders->date}}</span></h3>
                        <h3><span style="color: #ff0000;">Đơn hàng đã hủy</span> <span style="color: #00c56a;"> {{number_format($orders->wallet)}}{{$getdata->currency}}</span></h3>
                    </div>
                </div>
                @elseif ($orders->transaction_type == 2)
                <div class="order-details-box">
                    <div class="wallet-details-img">
                        <img src='{!! asset("public/front/images/ic_trRed.png") !!}' alt="" class="mt-1">
                    </div>
                    <div class="order-details-name mt-3">
                        <h3> {{$orders->order_number}} <span style="color: #000;">{{$orders->date}}</span></h3>
                        <h3><span style="color: #00c56a;">Đã đặt đơn hàng</span> <span style="color: #ff0000;"> - {{number_format($orders->wallet)}}{{$getdata->currency}}</span></h3>
                    </div>
                </div>
                @elseif ($orders->transaction_type == 3)
                <div class="order-details-box">
                    <div class="wallet-details-img">
                        <img src='{!! asset("public/front/images/ic_trGreen.png") !!}' alt="" class="mt-1">
                    </div>
                    <div class="order-details-name mt-3">
                        <a href="javascript:void(0)">
                            <a href="#">
                                <h3>Đã giới thiệu cho {{$orders->username}} <span style="color: #000;">{{$orders->date}}</span></h3>
                            </a>
                        </a>
                        <h3><span style="color: #00c56a;">Phần thưởng</span> <span style="color: #00c56a;">{{number_format($orders->wallet)}}{{$getdata->currency}}</span></h3>
                    </div>
                </div>
                @elseif($orders->transaction_type == 4)
                <div class="order-details-box">
                    <div class="wallet-details-img">
                        <img src='{!! asset("public/front/images/ic_trGreen.png") !!}' alt="" class="mt-1">
                    </div>
                    <div class="order-details-name mt-3">
                        <a href="javascript:void(0)">
                            <a href="#">
                                <h3>Nạp tiền vào ví<span style="color: #000;">{{$orders->date}}</span></h3>
                            </a>
                        </a>
                        <h3><span style="color: #00c56a;">Số tiền nạp</span> <span style="color: #00c56a;">{{number_format($orders->wallet)}}{{$getdata->currency}}</span></h3>
                    </div>
                </div>
                @endif
                @endforeach
                {!! $transaction_data->links() !!}
            </div>
        </div>
    </div>
    <!-- Modal Add Money RazorPay-->
    <div class="modal fade text-left" id="AddMoneypay" tabindex="-1" role="dialog" aria-labelledby="RditProduct" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600" id="RditProduct">Nạp tiền vào ví</label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="errorr" style="color: red;"></div>
                <form method="post" id="change_password_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <label>Số tiền cần nạp</label>
                        <div class="form-group">
                            <input type="text" name="add_money" id="add_money" class="form-control" required="">
                            <input type="hidden" name="user_id" id="user_id" class="form-control" value="{{Session::get('id')}}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn open comman" data-dismiss="modal" value="Hủy">
                        <input type="button" class="btn open comman addmoney" value="Tiếp tục">
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@include('front.theme.footer')
<script type="text/javascript">
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('body').on('click', '.addmoney', function(e) {
        var add_money = parseFloat($('#add_money').val());
        var user_id = $('#user_id').val();
        // alert(add_money);
        $.ajax({
            url: 'http://localhost/laravel/foodorder/rechargeMoney',
            type: 'get',
        }).done(function(response) {
            window.location.href = "http://localhost/laravel/foodorder/rechargeMoney";
        });
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: 'http://localhost/laravel/foodorder/recharge',
            type: 'post',
            dataType: 'json',
            data: {
                money: add_money,
                user_id: user_id,
            },
            success: function(result) {
                console.log(add_money);
            }
        });

    });

});
$('#add_money').keyup(function() {
    var val = $(this).val();
    if (isNaN(val)) {
        val = val.replace(/[^0-9\.]/g, '');
        if (val.split('.').length > 2)
            val = val.replace(/\.+$/, "");
    }
    $(this).val(val);
});

</script>
