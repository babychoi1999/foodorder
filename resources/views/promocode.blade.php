@extends('theme.default')
@section('content')
<div class="row page-titles mx-0">
    <div class="col p-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{URL::to('/admin/home')}}">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Promocode</a></li>
        </ol>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPromocode" data-whatever="@addPromocode">Thêm mã giảm giá</button>
        <!-- Add Promocode -->
        <div class="modal fade" id="addPromocode" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thêm mã giảm giá mới</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="add_promocode">
                        <div class="modal-body">
                            <span id="msg"></span>
                            @csrf
                            <div class="form-group">
                                <label for="offer_name" class="col-form-label">Tên mã:</label>
                                <input type="text" class="form-control" name="offer_name" id="offer_name" placeholder="Tên mã giảm giá">
                            </div>
                            <div class="form-group">
                                <label for="offer_code" class="col-form-label">Mã giảm giá(Ví dụ: 15SALE):</label>
                                <input type="text" class="form-control" name="offer_code" id="offer_code" placeholder="Mã giảm giá">
                            </div>
                            <div class="form-group">
                                <label for="offer_amount" class="col-form-label">Phần trăm giảm(%):</label>
                                <input type="text" class="form-control" name="offer_amount" id="offer_amount" placeholder="Phần trăm khuyến mại (%)">
                            </div>
                            <div class="form-group">
                                <label for="description" class="col-form-label">Mô tả:</label>
                                <textarea class="form-control" name="description" id="description" placeholder="Mô tả khuyến mại"></textarea>
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
        <!-- Edit Promocode -->
        <div class="modal fade" id="EditPromocode" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabeledit" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="post" name="editpromocode" class="editpromocode" id="editpromocode">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabeledit">Sửa mã giảm giá</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <span id="emsg"></span>
                        <div class="modal-body">
                            <input type="hidden" class="form-control" id="id" name="id">
                            <div class="form-group">
                                <label for="getoffer_name" class="col-form-label">Tên mã:</label>
                                <input type="text" class="form-control" name="getoffer_name" id="getoffer_name" placeholder="Nhập tên mã">
                            </div>
                            <div class="form-group">
                                <label for="getoffer_code" class="col-form-label">Mã giảm giá(Ví dụ: 15SALE):</label>
                                <input type="text" class="form-control" name="getoffer_code" id="getoffer_code" placeholder="Nhập mã giảm giá">
                            </div>
                            <div class="form-group">
                                <label for="getoffer_amount" class="col-form-label">Phần trăm giảm (%):</label>
                                <input type="text" class="form-control" name="getoffer_amount" id="getoffer_amount" placeholder="Nhập phần trăm khuyến mại (%)">
                            </div>
                            <div class="form-group">
                                <label for="get_description" class="col-form-label">Mô tả:</label>
                                <textarea class="form-control" name="get_description" id="get_description" placeholder="Mô tả khuyến mại"></textarea>
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
                    <h4 class="card-title">Danh sách mã giảm giá</h4>
                    <div class="table-responsive" id="table-display">
                        @include('theme.promocodetable')
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

    $('#add_promocode').on('submit', function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        $('#preloader').show();
        $.ajax({
            url: "{{ URL::to('admin/promocode/store') }}",
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
                    PromocodeTable();
                    $('#message').html(msg);
                    $("#addPromocode").modal('hide');
                    $("#add_promocode")[0].reset();
                    setTimeout(function() {
                        $('#message').html('');
                    }, 5000);
                }
            },
        })
    });

    $('#editpromocode').on('submit', function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        $('#preloader').show();
        $.ajax({
            url: "{{ URL::to('admin/promocode/update') }}",
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
                    PromocodeTable();
                    $('#message').html(msg);
                    $("#EditPromocode").modal('hide');
                    $("#editpromocode")[0].reset();
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
        url: "{{ URL::to('admin/promocode/show') }}",
        data: {
            id: id
        },
        method: 'POST', //Post method,
        dataType: 'json',
        success: function(response) {
            $('#preloader').hide();
            jQuery("#EditPromocode").modal('show');
            $('#id').val(response.ResponseData.id);
            $('#getoffer_name').val(response.ResponseData.offer_name);
            $('#getoffer_code').val(response.ResponseData.offer_code);
            $('#getoffer_amount').val(response.ResponseData.offer_amount);
            $('#get_description').val(response.ResponseData.description);
        },
        error: function(error) {
            $('#preloader').hide();
        }
    })
}

function StatusUpdate(id, status) {
    swal({
            title: "Bạn chắc chứ?",
            text: "Bạn có muốn xóa mã giảm giá này?",
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
                    url: "{{ URL::to('admin/promocode/status') }}",
                    data: {
                        id: id,
                        status: status
                    },
                    method: 'POST',
                    success: function(response) {
                        if (response == 1) {
                            swal({
                                    title: "Thành công!",
                                    text: "Đã xóa mã giảm giá.",
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
                                        PromocodeTable();
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

function PromocodeTable() {
    $('#preloader').show();
    $.ajax({
        url: "{{ URL::to('admin/promocode/list') }}",
        method: 'get',
        success: function(data) {
            $('#preloader').hide();
            $('#table-display').html(data);
            $(".zero-configuration").DataTable()
        }
    });
}

</script>
@endsection
