@include('front.theme.header')>
@if(Session::has('success'))
<div class="alert alert-success"> {{ Session::get('success') }}</div>
@endif
<section class="favourite">
    <div class="container">
        <h2 class="sec-head">Đơn hàng của tôi</h2>
        <div class="row">
            @if (count($orderdata) == 0)
            <p>Không tìm thấy đơn hàng nào</p>
            @else
            @foreach ($orderdata as $orders)
            <div class="col-lg-4">
                <a href="{{URL::to('order-details/'.$orders->id)}}" class="order-box">
                    <div class="order-box-no">
                        {{$orders->date}}
                        <h4>ID đơn hàng : <span style="font-size:16px">{{$orders->order_number}}</span></h4>
                        <span style="color: #fe734c; font-weight: 400">
                            @if($orders->payment_type == 1)
                            Razorpay Payment
                            @elseif($orders->payment_type == 2)
                            Stripe Payment
                            @elseif($orders->payment_type == 3)
                            Ví của tôi
                            @else
                            Thanh toán khi nhận hàng
                            @endif
                        </span>
                        @if($orders->status == 1)
                        <p class="order-status">Trạng thái : <span>Chờ xác nhận</span></p>
                        @elseif($orders->status == 2)
                        <p class="order-status">Trạng thái : <span>Đã xác nhận</span></p>
                        @elseif($orders->status == 3)
                        <p class="order-status">Trạng thái : <span>Đang giao</span></p>
                        @elseif($orders['status'] == 5)
                        <p class="order-status">Trạng thái : <span>Đã hủy</span></p>
                        @elseif($orders['status'] == 6)
                        <p class="order-status">Trạng thái : <span>Đã hủy</span></p>
                        @else
                        <p class="order-status">Trạng thái : <span>Đã giao</span></p>
                        @endif
                    </div>
                    <div class="order-box-price">
                        <h5>{{number_format($orders->total_price)}}{{$getdata->currency}}</h5>
                        @if($orders->order_type == 1)
                        Vận chuyển
                        @else
                        Tự lấy hàng
                        @endif
                    </div>
                </a>
            </div>
            @endforeach
            @endif
        </div>
        {!! $orderdata->links() !!}
    </div>
</section>
@include('front.theme.footer')
