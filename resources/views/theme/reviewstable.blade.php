<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>Họ tên</th>
            <th>Mức đánh giá</th>
            <th>Bình luận</th>
            <th>Thời gian tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php

        $i=1;

        foreach ($getreview as $reviews) {

        ?>
        <tr id="dataid{{$reviews->id}}">
            <td>{{$i}}</td>
            <td>{{$reviews['users']->name}}</td>
            <td><i class="fa fa-star"></i> {{$reviews->ratting}}</td>
            <td>{{$reviews->comment}}</td>
            <td>{{$reviews->created_at}}</td>
            <td>
                <span>
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="DeleteData('{{$reviews->id}}')" title="" data-original-title="Delete">
                        <span class="badge badge-danger">Xóa</span>
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
