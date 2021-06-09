@extends('theme.default')
@section('content')
<!-- row -->
<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Product Photos</a></li>
        </ol>
    </div>
</div>
<!-- row -->
<div class="container-fluid">
    <!-- End Row -->
    <div class="row">
        <div class="col-lg-4 col-xl-3">
            <div class="card category-card">
                <div class="card-body">
                    <h4>Thể loại</h4>
                    <p class="text-muted">{{$itemdetails->category_name}}</p>
                    <h4>Đơn giá</h4>
                    <p>{{number_format($itemdetails->item_price)}}{{Auth::user()->currency}}</p>
                    <h4>Thời gian vận chuyển</h4>
                    <p>{{$itemdetails->delivery_time}}</p>
                    <h4>Tên sản phẩm</h4>
                    <p class="text-muted">{{$itemdetails->item_name}}</p>
                    <h4>Mô tả</h4>
                    <p class="text-muted">{{$itemdetails->item_description}}</p>
                    <h4>Nguyên liệu</h4>
                    <div class="md-6">
                        <?php
                        // dd($getitemimages);
                        foreach ($getingredients as $ingredients) {

                        ?>
                        <div class="img-aside">
                            <img src='{!! asset("images/ingredients/".$ingredients->image) !!}' class="rounded img-thumbnail" style="width: 100px;">
                            <div class="img-aside-btn">
                                <button type="button" onClick="Editingredients({{$ingredients->id}})" class="btn mb-2 btn-sm btn-primary">Sửa</button>
                                @if (env('Environment') == 'sendbox')
                                <button type="button" class="btn mb-2 btn-sm btn-danger" onclick="myFunction()">Xóa</button>
                                @else
                                <button type="submit" onclick="Deleteingredients({{$ingredients->id}})" class="btn mb-2 btn-sm btn-danger">Xóa</button>
                                @endif
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-xl-9">
            <div id="success-msg" class="alert alert-dismissible mt-3" style="display: none;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Message!</strong> <span id="msg"></span>
            </div>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#AddProduct" data-whatever="@addProduct">Thêm ảnh sản phẩm</button>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#AddIngredients" data-whatever="@addIngredients">Thêm ảnh nguyên liệu</button>
            <div id="card-display">
                @include('theme.itemimage')
            </div>
        </div>
    </div>
    <!-- End Row -->
    <!-- Edit Images -->
    <div class="modal fade" id="EditImages" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabeledit" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" name="editimg" class="editimg" id="editimg" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabeledit">Ảnh sản phẩm</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <span id="emsg"></span>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Hình ảnh</label>
                            <input type="hidden" id="idd" name="id">
                            <input type="hidden" class="form-control" id="old_img" name="old_img">
                            <input type="file" class="form-control" name="image" id="image" accept="image/*">
                            <input type="hidden" name="removeimg" id="removeimg">
                        </div>
                        <div class="galleryim"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btna-secondary" data-dismiss="modal">Hủy</button>
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
    <!-- Edit Ingredient -->
    <div class="modal fade" id="EditIngredients" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabeledit" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" name="editingredients" class="editingredients" id="editingredients" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabeledit">Ảnh nguyên liệu</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <span id="iemsg"></span>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Hình ảnh</label>
                            <input type="hidden" id="idds" name="id">
                            <input type="hidden" class="form-control" id="old_imgs" name="old_img">
                            <input type="file" class="form-control" name="image" id="image" accept="image/*">
                            <input type="hidden" name="removeimg" id="removeimg">
                        </div>
                        <div class="galleryis"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btna-secondary" data-dismiss="modal">Hủy</button>
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
    <!-- Add Item Image -->
    <div class="modal fade" id="AddProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" name="addproduct" class="addproduct" id="addproduct" enctype="multipart/form-data">
                <span id="msg"></span>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thêm ảnh sản phẩm</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <span id="iiemsg"></span>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="colour" class="col-form-label">Chọn hình ảnh:</label>
                            <input type="file" multiple="true" class="form-control" name="file[]" id="file" accept="image/*" required="">
                        </div>
                        <div class="gallery"></div>
                        <input type="hidden" name="itemid" id="itemid" value="{{request()->route('id')}}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        @if (env('Environment') == 'sendbox')
                        <button type="button" class="btn btn-primary" onclick="myFunction()">Cập nhật</button>
                        @else
                        <button type="submit" name="submit" id="submit" class="btn btn-primary">Cập nhật</button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Add Ingredients Image -->
    <div class="modal fade" id="AddIngredients" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" name="addingredients" class="addingredients" id="addingredients" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thêm ảnh nguyên liệu</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <span id="aiemsg"></span>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="colour" class="col-form-label">Chọn hình ảnh:</label>
                            <input type="file" multiple="true" class="form-control" name="ingredients[]" id="ingredients" accept="image/*" required="">
                        </div>
                        <input type="hidden" name="itemid" id="itemid" value="{{request()->route('id')}}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        @if (env('Environment') == 'sendbox')
                        <button type="button" class="btn btn-primary" onclick="myFunction()">Cập nhật</button>
                        @else
                        <button type="submit" name="submit" id="submit" class="btn btn-primary">Cập nhật</button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- #/ container -->
<!-- #/ container -->
@endsection
@section('script')
<script type="text/javascript">
$(document).ready(function() {

    $('#addproduct').on('submit', function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        form_data.append('file', $('#file')[0].files);
        $('#preloader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ URL::to('admin/item/storeimages') }}",
            method: "POST",
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(result) {
                $('#preloader').hide();
                var msg = '';
                $('div.gallery').html('');
                if (result.error.length > 0) {
                    for (var count = 0; count < result.error.length; count++) {
                        msg += '<div class="alert alert-danger">' + result.error[count] + '</div>';
                    }
                    $('#iiemsg').html(msg);
                    setTimeout(function() {
                        $('#iiemsg').html('');
                    }, 5000);
                } else {
                    msg += '<div class="alert alert-success mt-1">' + result.success + '</div>';
                    $('#message').html(msg);
                    $("#AddProduct").modal('hide');
                    $("#addproduct")[0].reset();
                    location.reload();
                }
            },
        })
    });

    $('#addingredients').on('submit', function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        form_data.append('ingredients', $('#ingredients')[0].files);
        $('#preloader').show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ URL::to('admin/item/storeingredientsimages') }}",
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
                    $('#aiemsg').html(msg);
                    setTimeout(function() {
                        $('#aiemsg').html('');
                    }, 5000);
                } else {
                    msg += '<div class="alert alert-success mt-1">' + result.success + '</div>';
                    $('#message').html(msg);
                    $("#AddIngredients").modal('hide');
                    $("#addingredients")[0].reset();
                    location.reload();
                }
            },
        })
    });

    $('#editimg').on('submit', function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        $('#preloader').show();
        $.ajax({
            url: "{{ URL::to('admin/item/updateimage') }}",
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
                    location.reload();
                }
            },
        });
    });

    $('#editingredients').on('submit', function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        $('#preloader').show();
        $.ajax({
            url: "{{ URL::to('admin/item/updateingredients') }}",
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
                    $('#iemsg').html(msg);
                    setTimeout(function() {
                        $('#iemsg').html('');
                    }, 5000);
                } else {
                    location.reload();
                }
            },
        });
    });
});

function EditDocument(id) {
    $('#preloader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ URL::to('admin/item/showimage') }}",
        data: {
            id: id
        },
        method: 'POST', //Post method,
        dataType: 'json',
        success: function(response) {
            $('#preloader').hide();
            jQuery("#EditImages").modal('show');
            $('#idd').val(response.ResponseData.id);
            $('.galleryim').html("<img src=" + response.ResponseData.img + " class='img-fluid' style='max-height: 200px;'>");
            $('#old_img').val(response.ResponseData.image);
        },
        error: function(error) {
            $('#preloader').hide();
        }
    })
}

function Editingredients(id) {
    $('#preloader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ URL::to('admin/item/showingredients') }}",
        data: {
            id: id
        },
        method: 'POST', //Post method,
        dataType: 'json',
        success: function(response) {
            $('#preloader').hide();
            jQuery("#EditIngredients").modal('show');
            $('#idds').val(response.ResponseData.id);
            $('.galleryis').html("<img src=" + response.ResponseData.img + " class='img-fluid' style='max-height: 200px;'>");
            $('#old_imgs').val(response.ResponseData.image);
        },
        error: function(error) {
            $('#preloader').hide();
        }
    })
}

function DeleteImage(id, item_id) {
    swal({
            title: "Bạn chắc chứ?",
            text: "Bạn muốn xóa ảnh này?",
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
                    url: "{{ URL::to('admin/item/destroyimage') }}",
                    data: {
                        id: id,
                        item_id: item_id
                    },
                    method: 'POST',
                    success: function(response) {
                        if (response == 1) {
                            swal({
                                    title: "Thành công!",
                                    text: "Đã xóa ảnh",
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
                                        location.reload();
                                    }
                                });
                        } else if (response == 2) {

                            swal("Đã hủy", "Bạn không thể xóa ảnh này :(", "error");
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

function Deleteingredients(id) {
    swal({
            title: "Bạn chắc chứ?",
            text: "Bạn muốn xóa ảnh nguyên liệu này?",
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
                    url: "{{ URL::to('admin/item/destroyingredients') }}",
                    data: {
                        id: id
                    },
                    method: 'POST',
                    success: function(response) {
                        if (response == 1) {
                            swal({
                                    title: "Thành công!",
                                    text: "Đã xóa hình ảnh.",
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
                                        location.reload();
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

$(document).ready(function() {
    var imagesPreview = function(input, placeToInsertImagePreview) {
        if (input.files) {
            var filesAmount = input.files.length;
            $('div.gallery').html('');
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

    $('#file').on('change', function() {
        imagesPreview(this, 'div.gallery');
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
