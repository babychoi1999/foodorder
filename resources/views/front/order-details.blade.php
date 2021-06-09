@include('front.theme.header')>
<section class="order-details">
    <div class="container">
        <h2 class="sec-head">Chi tiết đơn hàng</h2>
        <p>({{$summery['order_number']}} - {{$summery['created_at']}})</p>
        @if($summery['order_type'] == 1)
        @if($summery['status'] == 1)
        <ul class="progressbar">
            <li class="active">Chờ xác nhận</li>
            <li>Đã xác nhận</li>
            <li>Đang giao</li>
            <li>Đã giao</li>
        </ul>
        @elseif($summery['status'] == 2)
        <ul class="progressbar">
            <li class="active">Chờ xác nhận</li>
            <li class="active">Đã xác nhận</li>
            <li>Đang giao</li>
            <li>Đã giao</li>
        </ul>
        @elseif($summery['status'] == 3)
        <ul class="progressbar">
            <li class="active">Chờ xác nhận</li>
            <li class="active">Đã xác nhận</li>
            <li class="active">Đang giao</li>
            <li>Đã giao</li>
        </ul>
        @elseif($summery['status'] == 4)
        <ul class="progressbar">
            <li class="active">Chờ xác nhận</li>
            <li class="active">Đã xác nhận</li>
            <li class="active">Đang giao</li>
            <li class="active">Đã giao</li>
        </ul>
        @elseif($summery['status'] == 5)
        <ul class="progressbar">
            <li class="active">Đơn hàng được hủy bởi bạn</li>
            <li>Đã xác nhận</li>
            <li>Đang giao</li>
            <li>Đã giao</li>
        </ul>
        @elseif($summery['status'] == 6)
        <ul class="progressbar">
            <li class="active">Đơn hàng được hủy bởi admin</li>
            <li>Đã xác nhận</li>
            <li>Đang giao</li>
            <li>Đã giao</li>
        </ul>
        @endif
        @else
        @if($summery['status'] == 1)
        <ul class="progressbar" style="text-align: center;">
            <li class="active">Chờ xác nhận</li>
            <li>Đã xác nhận</li>
            <li>Đã giao</li>
        </ul>
        @elseif($summery['status'] == 2)
        <ul class="progressbar" style="text-align: center;">
            <li class="active">Chờ xác nhận</li>
            <li class="active">Đã xác nhận</li>
            <li>Đã giao</li>
        </ul>
        @elseif($summery['status'] == 4)
        <ul class="progressbar" style="text-align: center;">
            <li class="active">Chờ xác nhận</li>
            <li class="active">Đã xác nhận</li>
            <li class="active">Đã giao</li>
        </ul>
        @elseif($summery['status'] == 5)
        <ul class="progressbar" style="text-align: center;">
            <li class="active">Đơn hàng được hủy bởi bạn</li>
            <li>Đã xác nhận</li>
            <li>Đã giao</li>
        </ul>
        @elseif($summery['status'] == 6)
        <ul class="progressbar" style="text-align: center;">
            <li class="active">Đơn hàng được hủy bởi admin</li>
            <li>Đã xác nhận</li>
            <li>Đã giao</li>
        </ul>
        @endif
        @endif
        <div class="row">
            <div class="col-lg-8">
                @foreach ($orderdata as $orders)
                <div class="order-details-box">
                    <div class="order-details-img">
                        <img src='{{$orders["itemimage"]->image }}' alt="">
                    </div>
                    <div class="order-details-name">
                        <a href="javascript:void(0)">
                            <a href="{{URL::to('product-details/'.$orders->id)}}">
                                <h3>{{$orders->item_name}} <span>{{number_format($orders->total_price)}}{{$getdata->currency}}</span></h3>
                            </a>
                        </a>
                        <p>Số lượng : {{$orders->qty}}</p>
                        @foreach ($orders['addons'] as $addons)
                        <div class="cart-addons-wrap">
                            <div class="cart-addons">
                                <b>{{$addons['name']}}</b> : {{number_format($addons['price'])}}{{$getdata->currency}}
                            </div>
                        </div>
                        @endforeach
                        @if ($orders->item_notes != "")
                        <p class="cart-pro-note">{{$orders->item_notes}}</p>
                        @endif
                    </div>
                </div>
                <?php
                    $data[] = array(
                        "total_price" => $orders->total_price
                    );
                ?>
                @endforeach
            </div>
            <div class="col-lg-4">
                <div class="order-payment-summary">
                    <h3>Chi tiết thanh toán</h3>
                    <p>Phí đơn hàng <span>{{number_format(array_sum(array_column(@$data, 'total_price')))}}{{$getdata->currency}}</span></p>
                    <p>Thuế({{$summery['tax']}}%) <span>{{number_format($summery['tax_amount'])}}{{$getdata->currency}}</span></p>
                    @if($summery['delivery_charge'] != "0")
                    <p>Phí vận chuyển <span>{{number_format(@$summery['delivery_charge'])}}{{$getdata->currency}}</span></p>
                    @endif
                    @if ($summery['promocode'] !="")
                    <p>Giảm giá ({{$summery['promocode']}}) <span>- {{number_format($summery['discount_amount'])}}{{$getdata->currency}}</span></p>
                    @endif
                    <?php
                    $a = array_sum(array_column(@$data, 'total_price'));
                    $b = array_sum(array_column(@$data, 'total_price'))*$summery['tax']/100;
                    $c = $summery['delivery_charge'];
                    $d = $summery['discount_amount'];
                    
                    if ($d == "NaN") {
                        $total = $a+$b+$c;
                    } else {
                        $total = $a+$b+$c-$d;
                    }
                    
                    ?>
                    <p class="order-details-total">Tổng cộng <span>{{number_format($total)}}{{$getdata->currency}}</span></p>
                </div>
                @if($summery['driver_name'] != "")
                <div class="order-add">
                    <h6>Thông tin tài xế</h6>
                    <div class="order-details-img">
                        <img src='{{$summery["driver_profile_image"]}}' alt="">
                    </div>
                    <p class="mt-3">{{$summery['driver_name']}}</p>
                    <p>
                        <a href="tel:{{$summery['driver_mobile']}}"> {{$summery['driver_mobile']}}</a>
                    </p>
                </div>
                @endif
                @if($summery['order_type'] == 1)
                <div class="order-add">
                    <h6>Địa chỉ giao hàng</h6>
                    <p>{{$summery['address']}}</p>
                    <h6>Số nhà</h6>
                    <p>{{$summery['building']}}</p>
                    <h6>Địa điểm</h6>
                    <p>{{$summery['landmark']}}</p>
                    <h6>Mã vùng</h6>
                    <p>{{$summery['pincode']}}</p>
                </div>
                @endif
                @if ($summery['order_notes'] !="")
                <div class="order-add">
                    <h6>Ghi chú</h6>
                    <p>{{$summery['order_notes']}}</p>
                </div>
                @endif
                @if ($summery['status'] == 1)
                <div class="delivery-btn-wrap">
                    <button type="button" class="btn open comman" onclick="OrderCancel('{{$summery['id']}}')">Hủy đơn hàng</button>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@include('front.theme.footer')
