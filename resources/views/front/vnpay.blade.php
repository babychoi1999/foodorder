<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Tạo mới đơn hàng</title>
    <!-- Bootstrap core CSS -->
    <link href="{{asset('public/vnpay_php/assets/bootstrap.min.css')}}" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="{{asset('public/vnpay_php/assets/jumbotron-narrow.css')}}" rel="stylesheet">
    <script src="{{asset('public/vnpay_php/assets/jquery-1.11.3.min.js')}}"></script>
</head>

<body>
    <div class="container">
        <div class="header clearfix">
            <h3 class="text-muted">Thanh toán qua VNPAY</h3>
        </div>
        <h3>Tạo mới đơn hàng</h3>
        <div class="table-responsive">
            <form action="{{url('/paybyvnpay')}}" id="create_form" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label for="language">Loại hàng hóa </label>
                    <select name="order_type" id="order_type" class="form-control">
                        <option value="topup">Nạp tiền vào ví</option>
                        <option value="billpayment">Thanh toán hóa đơn</option>
                        <option value="fashion">Thời trang</option>
                        <option value="other">Khác - Xem thêm tại VNPAY</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Số tiền</label>
                    <input class="form-control" id="amount" name="amount" value="{{$paid_amount}}" readonly="" type="number" />
                    {{-- <input class="form-control" id="amount" name="amount" @if($order_type=='1' ) value="{{$totalMoney}}" @else value="{{$order_total}}" @endif disabled="" type="number" /> --}}
                </div>
                <div class=" form-group">
                    <label for="order_desc">Nội dung thanh toán</label>
                    <textarea class="form-control" cols="20" id="order_desc" name="order_desc" rows="2">{{$payment_content}}</textarea>
                </div>
                <div class="form-group">
                    <label for="bank_code">Ngân hàng</label>
                    <select name="bank_code" id="bank_code" class="form-control">
                        <option value="">Không chọn</option>
                        <option value="NCB"> Ngan hang NCB</option>
                        <option value="AGRIBANK"> Ngan hang Agribank</option>
                        <option value="SCB"> Ngan hang SCB</option>
                        <option value="SACOMBANK">Ngan hang SacomBank</option>
                        <option value="EXIMBANK"> Ngan hang EximBank</option>
                        <option value="MSBANK"> Ngan hang MSBANK</option>
                        <option value="NAMABANK"> Ngan hang NamABank</option>
                        <option value="VNMART"> Vi dien tu VnMart</option>
                        <option value="VIETINBANK">Ngan hang Vietinbank</option>
                        <option value="VIETCOMBANK"> Ngan hang VCB</option>
                        <option value="HDBANK">Ngan hang HDBank</option>
                        <option value="DONGABANK"> Ngan hang Dong A</option>
                        <option value="TPBANK"> Ngân hàng TPBank</option>
                        <option value="OJB"> Ngân hàng OceanBank</option>
                        <option value="BIDV"> Ngân hàng BIDV</option>
                        <option value="TECHCOMBANK"> Ngân hàng Techcombank</option>
                        <option value="VPBANK"> Ngan hang VPBank</option>
                        <option value="MBBANK"> Ngan hang MBBank</option>
                        <option value="ACB"> Ngan hang ACB</option>
                        <option value="OCB"> Ngan hang OCB</option>
                        <option value="IVB"> Ngan hang IVB</option>
                        <option value="VISA"> Thanh toan qua VISA/MASTER</option>
                    </select>
                    <div class="form-group">
                        <label for="language">Ngôn ngữ</label>
                        <select name="language" id="language" class="form-control">
                            <option value="vn">Tiếng Việt</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" id="btnPopup">Xác nhận thanh toán</button>
                <button type="submit" class="btn btn-default">Quay trở lại</button>
            </form>
        </div>
        <p>
            &nbsp;
        </p>
        <footer class="footer">
            <p>&copy; VNPAY 2015</p>
        </footer>
    </div>
    <link href="https://sandbox.vnpayment.vn/paymentv2/lib/vnpay/vnpay.css" rel="stylesheet" />
    <script src="https://sandbox.vnpayment.vn/paymentv2/lib/vnpay/vnpay.js"></script>
    <script type="text/javascript">
    // $("#btnPopup").click(function() {
    //     // var token = $("input[name='_token']").val();
    //     var totalMoney = $('#amount').val();
    //     var language = $('#language').val();
    //     var bank_code = $('#bank_code').val();
    //     var order_desc = $('#order_desc').val();
    //     var order_type = $('#order_type').val();
    //     $.ajax({
    //         url: 'http://localhost/laravel/foodorder/public/paywithvnpay',
    //         type: 'POST',
    //         data: {
    //             "_token": "{{csrf_token()}}",
    //             // token: token,

    //             totalMoney: totalMoney,
    //             language: language,
    //             bank_code: bank_code,
    //             order_desc: order_desc,
    //             order_type: order_type

    //         }
    //     }).done(function(response) {
    //         console.log(totalMoney + " " + token);
    //         // RenderListCart(response);

    //     });
    //     //     function RenderListCart(response) {
    //     //     window.setTimeout(function() { location.reload() }, 2000);
    //     //     alertify.success('Đã xóa giỏ hàng');
    //     // }
    // });

    </script>
</body>

</html>
