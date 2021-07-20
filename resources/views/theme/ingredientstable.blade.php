<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>Hình ảnh</th>
            <th>Tên nguyên liệu</th>
            <th>Thời gian tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getingredients as $ingredients) {
        ?>
        <tr id="dataid{{$ingredients->id}}">
            <td>{{$ingredients->id}}</td>
            <td><img src='{!! asset("public/images/ingredients/".$ingredients->image) !!}' class='img-fluid' style='max-height: 50px;'></td>
            <td>{{$ingredients->ingredients}}</td>
            <td>{{$ingredients->created_at}}</td>
            @if (env('Environment') == 'sendbox')
            <td>
                <span>
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$ingredients->id}}')" title="" data-original-title="Edit">
                        <span class="badge badge-success">Sửa</span>
                    </a>
                    <a class="badge badge-danger px-2" onclick="myFunction()" style="color: #fff;">Xóa</a>
                </span>
            </td>
            @else
            <td>
                <span>
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$ingredients->id}}')" title="" data-original-title="Edit">
                        <span class="badge badge-success">Sửa</span>
                    </a>
                    <a class="badge badge-danger px-2" onclick="Delete('{{$ingredients->id}}')" style="color: #fff;">Xóa</a>
                </span>
            </td>
            @endif
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
