<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Thông tin thanh toán</title>
    <!-- Bootstrap core CSS -->
    <link href="{{asset('public/vnpay_php/assets/bootstrap.min.css')}}" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="{{asset('public/vnpay_php/assets/jumbotron-narrow.css')}}" rel="stylesheet">
    <script src="{{asset('public/vnpay_php/assets/jquery-1.11.3.min.js')}}"></script>
</head>

<body>
    <!--Begin display -->
    <div class="container">
        <div class="header clearfix">
            <h3 class="text-muted">Thông tin đơn hàng</h3>
        </div>
        <div class="table-responsive">
            @if(isset($order_number))
            <div class="form-group">
                <label>Mã đơn hàng:</label>
                <label>{{$order_number}}</label>
            </div>
            @endif
            <div class="form-group">
                <label>Tên khách hàng:</label>
                <label>{{$username}}</label>
            </div>
            <div class="form-group">
                <label>Số tiền:</label>
                <label>
                    {{number_format($vnpayData['vnp_Amount']/100)}} VNĐ</label>
            </div>
            <div class="form-group">
                <label>Nội dung thanh toán:</label>
                <label>{{$payment_content}}</label>
            </div>
            <div class="form-group">
                <label>Mã phản hồi (vnp_ResponseCode):</label>
                <label>
                    {{$vnpayData['vnp_ResponseCode']}}</label>
            </div>
            <div class="form-group">
                <label>Mã GD Tại VNPAY:</label>
                <label>
                    {{$vnpayData['vnp_TransactionNo']}}</label>
            </div>
            <div class="form-group">
                <label>Mã Ngân hàng:</label>
                <label>
                    {{$vnpayData['vnp_BankCode']}}</label>
            </div>
            <div class="form-group">
                <label>Thời gian thanh toán:</label>
                <label>
                    {{date('Y-m-d H:i'), strtotime($vnpayData['vnp_TxnRef'])}}</label>
            </div>
            <div class="form-group">
                <label>Kết quả: Giao dịch thành công</label>
                <label>
                </label>
                <br>
                <a href="{{url('/')}}">
                    <button>Quay lại trang chủ</button>
                </a>
            </div>
        </div>
        <p>
            &nbsp;
        </p>
        <footer class="footer">
            <p>&copy; Quản lý Tiếng Anh 2020</p>
        </footer>
    </div>
</body>

</html>
