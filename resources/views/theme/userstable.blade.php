<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>Hình ảnh</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th>Đăng nhập bằng</th>
            <th>Trạng thái xác thực</th>
            <th>Thời gian đăng ký</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getusers as $users) {
        ?>
        <tr id="dataid{{$users->id}}">
            <td>{{$users->id}}</td>
            <td><img src='{!! asset("images/profile/".$users->profile_image) !!}' style="width: 100px;"></td>
            <td>{{$users->name}}</td>
            <td>{{$users->email}}</td>
            <td>{{$users->mobile}}</td>
            <td>
                @if($users->login_type == "facebook")
                Facebook
                @elseif($users->login_type == "google")
                Google
                @else
                Email
                @endif
            </td>
            <td>
                @if($users->is_verified == "1")
                Đã xác thực
                @else
                Chưa xác thực
                @endif
            </td>
            <td>{{$users->created_at}}</td>
            <td>
                @if (env('Environment') == 'sendbox')
                <a href="#" data-toggle="tooltip" data-placement="top" onclick="myFunction()" title="" data-original-title="Block">
                    <span class="badge badge-danger">Chặn</span>
                </a>
                @else
                @if($users->is_available == '1')
                <a class="badge badge-danger px-2" onclick="StatusUpdate('{{$users->id}}','2')" style="color: #fff;">Chặn</a>
                @else
                <a class="badge badge-primary px-2" onclick="StatusUpdate('{{$users->id}}','1')" style="color: #fff;">Đã chặn</a>
                @endif
                @endif
                <a data-toggle="tooltip" href="{{URL::to('admin/user-details/'.$users->id)}}" data-original-title="View">
                    <span class="badge badge-warning">Chi tiết</span>
                </a>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
