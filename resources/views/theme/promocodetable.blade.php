<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>Tên mã</th>
            <th>Mã giảm giá</th>
            <th>Phần trăm khuyến mại (%) </th>
            <th>Mô tả </th>
            <th>Thời gian tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getpromocode as $promocode) {
        ?>
        <tr id="dataid{{$promocode->id}}">
            <td>{{$promocode->id}}</td>
            <td>{{$promocode->offer_name}}</td>
            <td>{{$promocode->offer_code}}</td>
            <td>{{$promocode->offer_amount}}</td>
            <td>{{$promocode->description}}</td>
            <td>{{$promocode->created_at}}</td>
            <td>
                <span>
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$promocode->id}}')" title="" data-original-title="Edit">
                        <span class="badge badge-success">Sửa</span>
                    </a>
                    @if (env('Environment') == 'sendbox')
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="myFunction()" title="" data-original-title="Delete">
                        <span class="badge badge-danger">Xóa</span>
                    </a>
                    @else
                    <a class="badge badge-danger px-2" onclick="StatusUpdate('{{$promocode->id}}','2')" style="color: #fff;">Xóa</a>
                    @endif
                </span>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
