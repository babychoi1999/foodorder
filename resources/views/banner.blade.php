@extends('theme.default')
@section('content')
<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Promotion Banner</a></li>
        </ol>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addBanner" data-whatever="@addBanner">Thêm banner</button>
        <!-- Add Promotion Banner -->
        <div class="modal fade" id="addBanner" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thêm banner mới</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="add_banner" enctype="multipart/form-data">
                        <div class="modal-body">
                            <span id="msg"></span>
                            @csrf
                            <div class="form-group">
                                <label for="image" class="col-form-label">Hình ảnh:</label>
                                <input type="file" class="form-control" name="image" id="image" required="" accept="image/*">
                                <input type="hidden" name="removeimg" id="removeimg">
                            </div>
                            <div class="gallery"></div>
                            <div class="form-group">
                                <label for="item_id" class="col-form-label">Sản phẩm:</label>
                                <select name="item_id" class="form-control selectpicker" data-live-search="true" id="item_id">
                                    <option value="">Thêm sản phẩm</option>
                                    <?php
                                foreach ($getitem as $item) {
                                ?>
                                    <option value="{{$item->id}}">{{$item->item_name}}</option>
                                    <?php
                                }
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                            @if (env('Environment') == 'sendbox')
                            <button type="button" class="btn btn-primary" onclick="myFunction()">Lưu</button>
                            @else
                            <button type="submit" class="btn btn-primary">Lưu</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Edit Promotion Banner -->
        <div class="modal fade" id="EditBanner" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabeledit" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="post" name="editbanner" class="editbanner" id="editbanner" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabeledit">Sửa banner</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <span id="emsg"></span>
                        <div class="modal-body">
                            <input type="hidden" class="form-control" id="id" name="id">
                            <input type="hidden" class="form-control" id="old_img" name="old_img">
                            <div class="form-group">
                                <label for="image" class="col-form-label">Chọn hình ảnh:</label>
                                <input type="file" class="form-control" name="image" id="image" accept="image/*">
                            </div>
                            <div class="gallerys"></div>
                            <div class="form-group">
                                <label for="item_id" class="col-form-label">Sản phẩm:</label>
                                <select name="item_id" class="form-control selectpicker" data-live-search="true" id="getitem_id">
                                    <option value="">Chọn sản phẩm</option>
                                    <?php
                                    foreach ($getitem as $item) {
                                    ?>
                                    <option value="{{$item->id}}">{{$item->item_name}}</option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                            @if (env('Environment') == 'sendbox')
                            <button type="button" class="btn btn-primary" onclick="myFunction()">Lưu</button>
                            @else
                            <button type="submit" class="btn btn-primary">Lưu</button>
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
                    <h4 class="card-title">Danh sách banner</h4>
                    <div class="table-responsive" id="table-display">
                        @include('theme.bannertable')
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
$(document).ready(function() {
    $('#add_banner').on('submit', function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        $('#preloader').show();
        $.ajax({
            url: "{{ URL::to('admin/banner/store') }}",
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
                    BannerTable();
                    $('#message').html(msg);
                    $("#addBanner").modal('hide');
                    $("#add_banner")[0].reset();
                    $('.gallery').html('');
                    setTimeout(function() {
                        $('#message').html('');
                    }, 5000);
                }
            },
        })
    });

    $('#editbanner').on('submit', function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        $('#preloader').show();
        $.ajax({
            url: "{{ URL::to('admin/banner/update') }}",
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
                    BannerTable();
                    $('#message').html(msg);
                    $("#EditBanner").modal('hide');
                    $("#editbanner")[0].reset();
                    $('.gallery').html('');
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
        url: "{{ URL::to('admin/banner/show') }}",
        data: {
            id: id
        },
        method: 'POST', //Post method,
        dataType: 'json',
        success: function(response) {
            $('#preloader').hide();
            jQuery("#EditBanner").modal('show');
            $('#id').val(response.ResponseData.id);
            $('#getitem_id').val(response.ResponseData.item_id);
            $('#getitem_id').selectpicker('refresh');

            $('.gallerys').html("<img src=" + response.ResponseData.image + " class='img-fluid' style='max-height: 200px;'>");
            $('#old_img').val(response.ResponseData.image);
        },
        error: function(error) {
            $('#preloader').hide();
        }
    })
}

function DeleteData(id) {
    swal({
            title: "Bạn chắc chứ?",
            text: "Bạn muốn xóa banner này?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Có!",
            cancelButtonText: "Không!",
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
                    url: "{{ URL::to('admin/banner/destroy') }}",
                    data: {
                        id: id
                    },
                    method: 'POST',
                    success: function(response) {
                        if (response == 1) {
                            swal({
                                    title: "Thành công!",
                                    text: "Đã xóa banner.",
                                    type: "success",
                                    showCancelButton: true,
                                    confirmButtonClass: "btn-danger",
                                    confirmButtonText: "Ok",
                                    closeOnConfirm: false,
                                    showLoaderOnConfirm: true,
                                },
                                function(isConfirm) {
                                    if (isConfirm) {
                                        $('#dataid' + id).remove();
                                        swal.close();
                                        // location.reload();
                                    }
                                });
                        } else {
                            swal("Đã hủy", "Đã có lỗi xảy ra :(", "error");
                        }
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

function StatusUpdate(id, status) {
    swal({
            title: "Are you sure?",
            text: "Do you want to change status of this banner?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, change it!",
            cancelButtonText: "No, cancel plz!",
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
                    url: "{{ URL::to('admin/banner/status') }}",
                    data: {
                        id: id,
                        status: status
                    },
                    method: 'POST',
                    success: function(response) {
                        if (response == 1) {
                            swal({
                                    title: "Thành công",
                                    text: "Trạng thái đã được thay đổi.",
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
                                        BannerTable();
                                    }
                                });
                        } else {
                            swal("Đã hủy", "Đã có lỗi xảy ra :(", "error");
                        }
                    },
                    error: function(e) {
                        swal("Đã hủy", "Đã có lỗi xảy ra :(", "error");
                    }
                });
            } else {
                swal("Đã hủy", ")", "error");
            }
        });
}

function BannerTable() {
    $('#preloader').show();
    $.ajax({
        url: "{{ URL::to('admin/banner/list') }}",
        method: 'get',
        success: function(data) {
            $('#preloader').hide();
            $('#table-display').html(data);
        }
    });
}

$(document).ready(function() {
    var imagesPreview = function(input, placeToInsertImagePreview) {
        if (input.files) {
            var filesAmount = input.files.length;
            $('.gallery').html('');
            $('.gallerys').html('');
            var n = 0;
            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    $($.parseHTML('<div>')).attr('class', 'imgdiv').attr('id', 'img_' + n).html('<img src="' + event.target.result + '" class="img-fluid">').appendTo(placeToInsertImagePreview);
                    n++;
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    };

    $('#image').on('change', function() {
        imagesPreview(this, '.gallerys');
        imagesPreview(this, '.gallery');
    });

});
var images = [];

function removeimg(id) {
    images.push(id);
    $("#img_" + id).remove();
    $('#remove_' + id).remove();
    $('#removeimg').val(images.join(","));
}

</script>
@endsection
