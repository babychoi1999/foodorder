<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>Ảnh đại diện</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th>Thời gian tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($getdriver as $driver)
        <tr id="dataid{{$driver->id}}">
            <td>{{$driver->id}}</td>
            <td><img src='{!! asset("images/profile/".$driver->profile_image) !!}' style="width: 100px;"></td>
            <td>{{$driver->name}}</td>
            <td>{{$driver->email}}</td>
            <td>{{$driver->mobile}}</td>
            <td>{{$driver->created_at}}</td>
            <td>
                <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$driver->id}}')" title="" data-original-title="Edit">
                    <span class="badge badge-success">Sửa</span>
                </a>
                @if (env('Environment') == 'sendbox')
                <a href="#" data-toggle="tooltip" data-placement="top" onclick="myFunction()" title="" data-original-title="Block">
                    <span class="badge badge-danger">Chặn</span>
                </a>
                @else
                @if($driver->is_available == '1')
                <a class="badge badge-danger px-2" onclick="StatusUpdate('{{$driver->id}}','2')" style="color: #fff;">Chặn</a>
                @else
                <a class="badge badge-primary px-2" onclick="StatusUpdate('{{$driver->id}}','1')" style="color: #fff;">Hủy chặn</a>
                @endif
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
