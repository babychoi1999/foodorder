<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>Thể loại</th>
            <th>Tên sản phẩm</th>
            <th>Giá</th>
            <th>Thời gian giao</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getitem as $item) {
        ?>
        <tr id="dataid{{$item->id}}">
            <td>{{$item->id}}</td>
            <td>{{@$item['category']->category_name}}</td>
            <td>{{$item->item_name}}</td>
            <td>{{number_format($item->item_price)}}{{Auth::user()->currency}}</td>
            <td>{{$item->delivery_time}}</td>
            @if (env('Environment') == 'sendbox')
            <td>
                @if ($item->item_status == 1)
                <a class="badge badge-success px-2" onclick="myFunction()" style="color: #fff;">Có sẵn</a>
                @else
                <a class="badge badge-danger px-2" onclick="myFunction()" style="color: #fff;">Tạm hết</a>
                @endif
            </td>
            <td>
                <span>
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$item->id}}')" title="" data-original-title="Edit">
                        <span class="badge badge-success">Sửa</span>
                    </a>
                    <a class="badge badge-danger px-2" onclick="myFunction()" style="color: #fff;">Xóa</a>
                    <a data-toggle="tooltip" href="{{URL::to('admin/item-images/'.$item->id)}}" data-original-title="View">
                        <span class="badge badge-warning">Chi tiết</span>
                    </a>
                </span>
            </td>
            @else
            <td>
                @if ($item->item_status == 1)
                <a class="badge badge-success px-2" onclick="StatusUpdate('{{$item->id}}','2')" style="color: #fff;">Có sẵn</a>
                @else
                <a class="badge badge-danger px-2" onclick="StatusUpdate('{{$item->id}}','1')" style="color: #fff;">Tạm hết</a>
                @endif
            </td>
            <td>
                <span>
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$item->id}}')" title="" data-original-title="Edit">
                        <span class="badge badge-success">Sửa</span>
                    </a>
                    <a class="badge badge-danger px-2" onclick="Delete('{{$item->id}}')" style="color: #fff;">Xóa</a>
                    <a data-toggle="tooltip" href="{{URL::to('admin/item-images/'.$item->id)}}" data-original-title="View">
                        <span class="badge badge-warning">Chi tiết</span>
                    </a>
                </span>
            </td>
            @endif
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
