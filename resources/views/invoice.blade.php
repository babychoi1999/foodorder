@extends('theme.default')
<style type="text/css">
@media print {

    @page {
        margin: 0;
    }

    body {
        margin: 1.6cm;
    }

}

</style>
@section('content')
<!-- row -->
<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Invoice</a></li>
        </ol>
    </div>
</div>
<!-- row -->
<div class="container-fluid">
    <!-- End Row -->
    <div class="card" id="printDiv">
        <div class="card-header">
            Hóa đơn
            <strong>{{$getusers->order_number}}</strong>
            <span class="float-right"> <strong>Trạng thái:</strong>
                @if($getusers->status == '1')
                Đang chờ xác nhận
                @elseif ($getusers->status == '2')
                Đã sẵn sàng
                @elseif ($getusers->status == '3')
                Đã giao cho nhà vận chuyển
                @elseif ($getusers->status == '4')
                Đã giao
                @else
                Đã hủy
                @endif
            </span>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-8">
                    <h6 class="mb-3">To:</h6>
                    <div>
                        <strong>{{$getusers['users']->name}}</strong>
                    </div>
                    <div>{{$getusers->address}}</div>
                    <div>Email: {{$getusers['users']->email}}</div>
                    <div>Số điện thoại: {{$getusers['users']->mobile}}</div>
                </div>
                @if ($getusers->order_notes !="")
                <div class="col-sm-4">
                    <h6 class="mb-3">Ghi chú:</h6>
                    <div>{{$getusers->order_notes}}</div>
                </div>
                @endif
            </div>
            <div class="table-responsive-sm">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="center">#</th>
                            <th>Sản phẩm</th>
                            <th class="right">Đơn giá</th>
                            <th class="center">Số lượng</th>
                            <th class="right">Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $i=1;

                        foreach ($getorders as $orders) {

                        ?>
                        <tr>
                            <td class="center">{{$i}}</td>
                            <td class="left strong">
                                {{$orders->item_name}}
                                @foreach ($orders['addons'] as $addons)
                                <div class="cart-addons-wrap">
                                    <div class="cart-addons">
                                        <b>{{$addons['name']}}</b> : {{number_format($addons['price'])}}{{Auth::user()->currency}}
                                    </div>
                                </div>
                                @endforeach
                                @if ($orders->item_notes != "")
                                <b>Item Notes</b> : {{$orders->item_notes}}
                                @endif
                            </td>
                            <td class="left">{{number_format($orders->item_price)}}{{Auth::user()->currency}}</td>
                            <td class="center">{{$orders->qty}}</td>
                            <td class="right">{{number_format($orders->total_price)}}{{Auth::user()->currency}}</td>
                        </tr>
                        <?php

                            $data[] = array(

                                "total_price" => $orders->total_price

                            );

                        ?>
                        <?php

                        $i++;

                        }

                        ?>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-lg-4 col-sm-5">
                </div>
                <div class="col-lg-4 col-sm-5 ml-auto">
                    <table class="table table-clear">
                        <tbody>
                            <tr>
                                <td class="left">
                                    <strong>Thuế</strong> ({{$getusers->tax}}%)
                                </td>
                                <td class="right">
                                    <strong>{{number_format($getusers->tax_amount)}}{{Auth::user()->currency}}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="left">
                                    <strong>Phí vận chuyển</strong>
                                </td>
                                <td class="right">
                                    <strong>{{number_format($getusers->delivery_charge)}}{{Auth::user()->currency}}</strong>
                                </td>
                            </tr>
                            @if ($getusers->discount_amount != 0)
                            <tr>
                                <td class="left">
                                    <strong>Khuyến mãi</strong> ({{$getusers->promocode}})
                                </td>
                                <td class="right">
                                    <strong>{{number_format($getusers->discount_amount)}}{{Auth::user()->currency}}</strong>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td class="left">
                                    <strong>Tổng tiền</strong>
                                </td>
                                <td class="right">
                                    <strong>{{number_format($getusers->order_total)}}{{Auth::user()->currency}}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->
    <button type="button" class="btn btn-primary float-right" id="doPrint">
        <i class="fa fa-print" aria-hidden="true"></i> In
    </button>
</div>
<!-- #/ container -->
<!-- #/ container -->
@endsection
@section('script')
<script type="text/javascript">
$(document).on('click', '.btn', function(event) {

    var printContents = document.getElementById('printDiv').innerHTML;

    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;

});

</script>
@endsection
