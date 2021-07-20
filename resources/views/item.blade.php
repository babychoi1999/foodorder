@extends('theme.default')
@section('content')
<style type="text/css">
.pac-container {
    z-index: 10000 !important;
}

</style>
<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Item</a></li>
        </ol>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addProduct" data-whatever="@addProduct">Thêm sản phẩm</button>
        <!-- Add Item -->
        <div class="modal fade" id="addProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thêm sản phẩm mới</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="add_product" enctype="multipart/form-data">
                        <div class="modal-body">
                            <span id="msg"></span>
                            @csrf
                            <div class="row">
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="cat_id" class="col-form-label">Loại sản phẩm:</label>
                                        <select name="cat_id" class="form-control" id="cat_id">
                                            <option value="">Chọn loại sản phẩm</option>
                                            <?php
                                        foreach ($getcategory as $category) {
                                        ?>
                                            <option value="{{$category->id}}">{{$category->category_name}}</option>
                                            <?php
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="item_name" class="col-form-label">Tên sản phẩm:</label>
                                        <input type="text" class="form-control" name="item_name" id="item_name" placeholder="Nhập tên sản phẩm">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="price" class="col-form-label">Giá:</label>
                                        <input type="text" class="form-control" name="price" id="price" placeholder="Nhập giá">
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="delivery_time" class="col-form-label">Thời gian vận chuyển:</label>
                                        <input type="text" class="form-control" name="delivery_time" id="delivery_time" placeholder="Nhập thời gian vận chuyển">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="addons_id" class="col-form-label">Chọn sản phẩm thêm:</label>
                                        <select name="addons_id[]" class="form-control selectpicker" multiple data-live-search="true" id="addons_id">
                                            {{-- <option value="">Chọn sản phẩm thêm</option>
                                            --}}
                                            <?php
                                        foreach ($getaddons as $addons) {
                                        ?>
                                            <option value="{{$addons->id}}">{{$addons->name}}</option>
                                            <?php
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="ingredients_id" class="col-form-label">Chọn nguyên liệu :</label>
                                        <select name="ingredients_id[]" class="form-control selectpicker" multiple data-live-search="true" id="ingredients_id">
                                            {{-- <option value="">Chọn nguyên liệu </option> --}}
                                            <?php
                                        foreach ($getingredients as $ingredients) {
                                        ?>
                                            <option value="{{$ingredients->id}}">{{$ingredients->ingredients}}</option>
                                            <?php
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 col-md-12">
                                    <div class="form-group">
                                        <label for="getprice" class="col-form-label">Mô tả:</label>
                                        <textarea class="form-control" rows="5" name="description" id="description" placeholder="Nhập mô tả"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="colour" class="col-form-label">Hình ảnh:</label>
                                        <input type="file" multiple="true" class="form-control" name="file[]" id="file" required="" accept="image/*">
                                        <input type="hidden" name="removeimg" id="removeimg">
                                    </div>
                                    <div class="gallery"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
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
        <!-- Edit Item -->
        <div class="modal fade" id="EditProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabeledit" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form method="post" name="editproduct" class="editproduct" id="editproduct" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabeledit">Chỉnh sửa sản phẩm</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <span id="emsg"></span>
                        <div class="modal-body">
                            <input type="hidden" class="form-control" id="id" name="id">
                            <div class="row">
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="getcat_id" class="col-form-label">Loại sản phẩm:</label>
                                        <select name="getcat_id" class="form-control" id="getcat_id">
                                            <option value="">Chọn loại sản phẩm</option>
                                            <?php
                                            foreach ($getcategory as $category) {
                                            ?>
                                            <option value="{{$category->id}}">{{$category->category_name}}</option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="getitem_name" class="col-form-label">Tên sản phẩm:</label>
                                        <input type="text" class="form-control" id="getitem_name" name="item_name" placeholder="Nhập tên sản phẩm">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="getprice" class="col-form-label">Giá bán:</label>
                                        <input type="text" class="form-control" name="getprice" id="getprice" placeholder="Nhập giá bán">
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="getdelivery_time" class="col-form-label">Thời gian vận chuyển:</label>
                                        <input type="text" class="form-control" name="getdelivery_time" id="getdelivery_time" placeholder="Nhập thời gian vận chuyển">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="getaddons_id" class="col-form-label">Chọn sản phẩm thêm:</label>
                                        <select name="addons_id[]" class="form-control selectpicker" multiple data-live-search="true" id="getaddons_id">
                                            {{-- <option value="">Chọn sản phẩm thêm</option> --}}
                                            <?php
                                            foreach ($getaddons as $addons) {
                                            ?>
                                            <option value="{{$addons->id}}">{{$addons->name}}</option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-6">
                                    <div class="form-group">
                                        <label for="getingredients_id" class="col-form-label">Chọn nguyên liệu :</label>
                                        <select name="ingredients_id[]" class="form-control selectpicker" multiple data-live-search="true" id="getingredients_id">
                                            {{-- <option value="">Chọn nguyên liệu</option> --}}
                                            <?php
                                            foreach ($getingredients as $ingredients) {
                                            ?>
                                            <option value="{{$ingredients->id}}">{{$ingredients->ingredients}}</option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 col-md-12">
                                    <div class="form-group">
                                        <label for="getprice" class="col-form-label">Mô tả:</label>
                                        <textarea class="form-control" rows="5" name="getdescription" id="getdescription" placeholder="Nhập mô tả"></textarea>
                                    </div>
                                </div>
                            </div>
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
    </div>
</div>
<!-- row -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <span id="message"></span>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Danh sách sản phẩm</h4>
                    <div class="table-responsive" id="table-display">
                        @include('theme.itemtable')
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

    $('#add_product').on('submit', function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        form_data.append('file', $('#file')[0].files);
        $('#preloader').show();
        $.ajax({
            url: "{{ URL::to('admin/item/store') }}",
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
                    $('#msg').html(msg);
                    setTimeout(function() {
                        $('#msg').html('');
                    }, 5000);
                } else {
                    msg += '<div class="alert alert-success mt-1">' + result.success + '</div>';
                    ProductTable();
                    $('#message').html(msg);
                    $("#addProduct").modal('hide');
                    $("#add_product")[0].reset();
                    setTimeout(function() {
                        $('#message').html('');
                    }, 5000);
                }
            },
        })
    });

    $('#editproduct').on('submit', function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        $('#preloader').show();
        $.ajax({
            url: "{{ URL::to('admin/item/update') }}",
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
                    ProductTable();
                    $('#message').html(msg);
                    $("#EditProduct").modal('hide');
                    $("#editproduct")[0].reset();
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
        url: "{{ URL::to('admin/item/show') }}",
        data: {
            id: id
        },
        method: 'POST', //Post method,
        dataType: 'json',
        success: function(response) {
            $('#preloader').hide();
            jQuery("#EditProduct").modal('show');
            $('#id').val(response.ResponseData.id);
            $('#getcat_id').val(response.ResponseData.cat_id);
            $('#getitem_name').val(response.ResponseData.item_name);
            $('#getprice').val(response.ResponseData.item_price);
            $('#getdelivery_time').val(response.ResponseData.delivery_time);
            $('#getdescription').val(response.ResponseData.item_description);

            if (response.ResponseData.addons_id != null) {
                var addons_id = response.ResponseData.addons_id.split(",");

                $("#getaddons_id option:selected").each(function() {
                    $(this).removeAttr("selected");
                });


                addons_id.forEach(function(d) {
                    $('#getaddons_id option[value="' + d + '"]').attr('selected', 'selected');
                });
                $('#getaddons_id').selectpicker('refresh');
            }

            if (response.ResponseData.ingredients_id != null) {
                var ingredients_id = response.ResponseData.ingredients_id.split(",");

                $("#getingredients_id option:selected").each(function() {
                    $(this).removeAttr("selected");
                });

                ingredients_id.forEach(function(d) {
                    $('#getingredients_id option[value="' + d + '"]').attr('selected', 'selected');
                });
                $('#getingredients_id').selectpicker('refresh');
            }
        },
        error: function(error) {
            $('#preloader').hide();
        }
    })
}

function StatusUpdate(id, status) {
    swal({
            title: "Bạn chắc chứ?",
            text: "Bạn thực sự muốn thay đổi trạng thái ?",
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
                    url: "{{ URL::to('admin/item/status') }}",
                    data: {
                        id: id,
                        status: status
                    },
                    method: 'POST', //Post method,
                    dataType: 'json',
                    success: function(response) {
                        swal({
                                title: "Thành công!",
                                text: "Đã thay đổi trạng thái sản phẩm.",
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
                                    ProductTable();
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

function Delete(id) {
    swal({
            title: "Bạn chắc chứ?",
            text: "Bạn có muốn xóa sản phẩm ?",
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
                    url: "{{ URL::to('admin/item/delete') }}",
                    data: {
                        id: id
                    },
                    method: 'POST', //Post method,
                    dataType: 'json',
                    success: function(response) {
                        swal({
                                title: "Thành công",
                                text: "Sản phẩm đã được xóa.",
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
                                    ProductTable();
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

function ProductTable() {
    $('#preloader').show();
    $.ajax({
        url: "{{ URL::to('admin/item/list') }}",
        method: 'get',
        success: function(data) {
            $('#preloader').hide();
            $('#table-display').html(data);
            $(".zero-configuration").DataTable({
                aaSorting: [
                    [0, 'DESC']
                ]
            })
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
    input.replaceWith(input.val('').clone(true));
}

$('#price').keyup(function() {
    var val = $(this).val();
    if (isNaN(val)) {
        val = val.replace(/[^0-9\.]/g, '');
        if (val.split('.').length > 2)
            val = val.replace(/\.+$/, "");
    }
    $(this).val(val);
});

$('#getprice').keyup(function() {
    var val = $(this).val();
    if (isNaN(val)) {
        val = val.replace(/[^0-9\.]/g, '');
        if (val.split('.').length > 2)
            val = val.replace(/\.+$/, "");
    }
    $(this).val(val);
});

</script>
@endsection
