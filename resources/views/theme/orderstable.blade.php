<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>Tên khách hàng</th>
            <th>Mã đơn hàng</th>
            <th>Hình thức thanh toán</th>
            <th>ID thanh toán</th>
            <th>Loại đơn hàng</th>
            <th>Trạng thái</th>
            <th>Shipper</th>
            <th>Thời gian đặt</th>
            <th>Thay đổi trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($getorders as $orders) {
        ?>
        <tr id="dataid{{$orders->id}}">
            <td>{{$i}}</td>
            <td>{{$orders['users']->name}}</td>
            <td>{{$orders->order_number}}</td>
            <td>
                @if($orders->payment_type =='0')
                COD
                @elseif($orders->payment_type =='1')
                RazorPay
                @elseif($orders->payment_type =='2')
                Stripe
                @elseif($orders->payment_type =='3')
                Ví của tôi
                @endif
            </td>
            <td>
                @if($orders->razorpay_payment_id == '')
                --
                @else
                {{$orders->razorpay_payment_id}}
                @endif
            </td>
            <td>
                @if($orders->order_type == 1)
                Vận chuyển
                @else
                Tự lấy hàng
                @endif
            </td>
            <td>
                @if($orders->status == '1')
                Đang chờ xác nhận
                @elseif ($orders->status == '2')
                Đã sẵn sàng
                @elseif ($orders->status == '3')
                Đã giao cho nhà vận chuyển
                @elseif ($orders->status == '4')
                Đã giao
                @else
                Đã hủy
                @endif
            </td>
            <td>
                @if ($orders->name == "")
                --
                @else
                {{$orders->name}}
                @endif
            </td>
            <td>{{$orders->created_at}}</td>
            <td>
                @if($orders->status == '1')
                <a data-toggle="tooltip" data-placement="top" onclick="StatusUpdate('{{$orders->id}}','2')" title="" data-original-title="Order Received">
                    <span class="badge badge-secondary px-2" style="color: #fff;">Chờ xác nhận</span>
                </a>
                @elseif ($orders->status == '2')
                @if ($orders->order_type == '2')
                <a class="badge badge-primary px-2" onclick="StatusUpdate('{{$orders->id}}','4')" style="color: #fff;">Đã sẵn sàng</a>
                @else
                <a class="open-AddBookDialog badge badge-primary px-2" data-toggle="modal" data-id="{{$orders->id}}" data-target="#myModal" style="color: #fff;">Giao cho nhà vận chuyển</a>
                @endif
                @elseif ($orders->status == '3')
                <a data-toggle="tooltip" data-placement="top" title="" data-original-title="Out for Delivery">
                    <span class="badge badge-success px-2" style="color: #fff;">Đã giao cho nhà vận chuyển</span>
                </a>
                @elseif ($orders->status == '4')
                <a data-toggle="tooltip" data-placement="top" title="" data-original-title="Out for Delivery">
                    <span class="badge badge-success px-2" style="color: #fff;">Đã giao</span>
                </a>
                @else
                <span class="badge badge-danger px-2">Đã hủy</span>
                @endif
                @if ($orders->status != '4' && $orders->status != '5' && $orders->status != '6')
                <a data-toggle="tooltip" data-placement="top" onclick="StatusUpdate('{{$orders->id}}','6')" title="" data-original-title="Cancel">
                    <span class="badge badge-danger px-2" style="color: #fff;">Hủy</span>
                </a>
                @endif
            </td>
            <td>
                <span>
                    <a data-toggle="tooltip" href="{{URL::to('admin/invoice/'.$orders->id)}}" data-original-title="View">
                        <span class="badge badge-warning">Chi tiết</span>
                    </a>
                </span>
            </td>
        </tr>
        <?php
        $i++;
        }
        ?>
    </tbody>
</table>
