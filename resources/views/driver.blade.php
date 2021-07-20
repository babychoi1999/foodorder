@extends('theme.default')
@section('content')
<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Driver</a></li>
        </ol>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addDriver" data-whatever="@addDriver">Thêm tài xế</button>
        <!-- Add Driver -->
        <div class="modal fade" id="addDriver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thêm tài xế mới</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="add_driver" enctype="multipart/form-data">
                        <div class="modal-body">
                            <span id="msg"></span>
                            @csrf
                            <div class="form-group">
                                <label for="name" class="col-form-label">Tên tài xế:</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Tên tài xế">
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-form-label">Email:</label>
                                <input type="text" class="form-control" name="email" id="email" placeholder="Email">
                            </div>
                            <div class="form-group">
                                <label for="mobile" class="col-form-label">Số điện thoại:</label>
                                <input type="text" class="form-control" name="mobile" id="mobile" placeholder="Số điện thoại">
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-form-label">Mật khẩu:</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Mật khẩu">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                            @if (env('Environment') == 'sendbox')
                            <button type="button" class="btn btn-primary" onclick="myFunction()">Thêm</button>
                            @else
                            <button type="submit" class="btn btn-primary">Thêm</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Edit Driver -->
        <div class="modal fade" id="EditDriver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabeledit" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="post" name="editdriver" class="editdriver" id="editdriver" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabeledit">Sửa thông tin tài xế</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <span id="emsg"></span>
                        <div class="modal-body">
                            <input type="hidden" class="form-control" id="id" name="id">
                            <input type="hidden" class="form-control" id="old_img" name="old_img">
                            <div class="form-group">
                                <label for="driver_id" class="col-form-label">Tên tài xế:</label>
                                <input type="text" class="form-control" id="get_name" name="name" placeholder="Tên tài xế">
                            </div>
                            <div class="form-group">
                                <label for="get_email" class="col-form-label">Email:</label>
                                <input type="text" class="form-control" name="email" id="get_email">
                            </div>
                            <div class="form-group">
                                <label for="get_mobile" class="col-form-label">Số điện thoại:</label>
                                <input type="text" class="form-control" name="mobile" id="get_mobile">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                            @if (env('Environment') == 'sendbox')
                            <button type="button" class="btn btn-primary" onclick="myFunction()">Cập nhật</button>
                            @else
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- row -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <span id="message"></span>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Danh sách tài xế</h4>
                    <div class="table-responsive" id="table-display">
                        @include('theme.drivertable')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- #/ container -->
@endsection
@section('script')
<script type="text/javascript">
$('.table').dataTable({
    aaSorting: [
        [0, 'DESC']
    ]
});
$(document).ready(function() {

    $('#add_driver').on('submit', function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        $('#preloader').show();
        $.ajax({
            url: "{{ URL::to('admin/driver/store') }}",
            method: "POST",
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(result) {
                $('#preloader').hide();
                var msg = '';
                if (result.error.length > 0) {
                    for (var count = 0; count < result.error.length; count++) {
                        msg += '<div class="alert alert-danger">' + result.error[count] + '</div>';
                    }
                    $('#msg').html(msg);
                    setTimeout(function() {
                        $('#msg').html('');
                    }, 5000);
                } else {
                    msg += '<div class="alert alert-success mt-1">' + result.success + '</div>';
                    DriverTable();
                    $('#message').html(msg);
                    $("#addDriver").modal('hide');
                    $("#add_driver")[0].reset();
                    setTimeout(function() {
                        $('#message').html('');
                    }, 5000);
                }
            },
        })
    });

    $('#editdriver').on('submit', function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        $('#preloader').show();
        $.ajax({
            url: "{{ URL::to('admin/driver/update') }}",
            method: 'POST',
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(result) {
                $('#preloader').hide();
                var msg = '';
                if (result.error.length > 0) {
                    for (var count = 0; count < result.error.length; count++) {
                        msg += '<div class="alert alert-danger">' + result.error[count] + '</div>';
                    }
                    $('#emsg').html(msg);
                    setTimeout(function() {
                        $('#emsg').html('');
                    }, 5000);
                } else {
                    msg += '<div class="alert alert-success mt-1">' + result.success + '</div>';
                    DriverTable();
                    $('#message').html(msg);
                    $("#EditDriver").modal('hide');
                    $("#editdriver")[0].reset();
                    setTimeout(function() {
                        $('#message').html('');
                    }, 5000);
                }
            },
        });
    });
});

function GetData(id) {
    $('#preloader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ URL::to('admin/driver/show') }}",
        data: {
            id: id
        },
        method: 'POST', //Post method,
        dataType: 'json',
        success: function(response) {
            $('#preloader').hide();
            jQuery("#EditDriver").modal('show');
            $('#id').val(response.ResponseData.id);
            $('#get_name').val(response.ResponseData.name);
            $('#get_email').val(response.ResponseData.email);
            $('#get_mobile').val(response.ResponseData.mobile);

            $('.gallerys').html("<img src=" + response.ResponseData.image + " class='img-fluid' style='max-height: 200px;'>");
            $('#old_img').val(response.ResponseData.image);
        },
        error: function(error) {
            $('#preloader').hide();
        }
    })
}

function StatusUpdate(id, status) {
    swal({
            title: "Bạn chắc chứ?",
            text: "Bạn có muốn chặn tài xế này ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Có",
            cancelButtonText: "Không",
            closeOnConfirm: false,
            closeOnCancel: false,
            showLoaderOnConfirm: true,
        },
        function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ URL::to('admin/driver/status') }}",
                    data: {
                        id: id,
                        status: status
                    },
                    method: 'POST', //Post method,
                    dataType: 'json',
                    success: function(response) {
                        swal({
                                title: "Thành công!",
                                text: "Đã chặn tài xế này.",
                                type: "success",
                                showCancelButton: true,
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "Ok",
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true,
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    swal.close();
                                    DriverTable();
                                }
                            });
                    },
                    error: function(e) {
                        swal("Đã hủy", "Đã có lỗi xảy ra :(", "error");
                    }
                });
            } else {
                swal("Đã hủy", "", "error");
            }
        });
}

function DriverTable() {
    $('#preloader').show();
    $.ajax({
        url: "{{ URL::to('admin/driver/list') }}",
        method: 'get',
        success: function(data) {
            $('#preloader').hide();
            $('#table-display').html(data);
            $(".zero-configuration").DataTable()
        }
    });
}

$('#mobile').on('input', function(event) {
    this.value = this.value.replace(/[^0-9]/g, '');
});

$('#get_mobile').on('input', function(event) {
    this.value = this.value.replace(/[^0-9]/g, '');
});

</script>
@endsection
