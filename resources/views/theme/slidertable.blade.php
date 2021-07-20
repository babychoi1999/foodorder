<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>Hình ảnh</th>
            <th>Tên slide</th>
            <th>Mô tả</th>
            <th>Thời gian tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getslider as $slider) {
        ?>
        <tr id="dataid{{$slider->id}}">
            <td>{{$slider->id}}</td>
            <td><img src='{!! asset("public/images/slider/".$slider->image) !!}' class='img-fluid' style='max-height: 50px;'></td>
            <td>{{$slider->title}}</td>
            <td>{{$slider->description}}</td>
            <td>{{$slider->created_at}}</td>
            <td>
                <span>
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$slider->id}}')" title="" data-original-title="Edit">
                        <span class="badge badge-success">Sửa</span>
                    </a>
                    @if (env('Environment') == 'sendbox')
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="myFunction()" title="" data-original-title="Delete">
                        <span class="badge badge-danger">Xóa</span>
                    </a>
                    @else
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="DeleteData('{{$slider->id}}')" title="" data-original-title="Delete">
                        <span class="badge badge-danger">Xóa</span>
                    </a>
                    @endif
                </span>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
