@include('front.theme.header')>
<style>
    .pac-container {
        z-index: 10000 !important;
    }
    .error {
        color: red;
    }
</style>
<section class="favourite">
    <div class="container">
        <h2 class="sec-head">Địa chỉ của tôi</h2>
        @if (\Session::has('success'))
        <div class="alert alert-success" style="text-align: center;">
            {!! \Session::get('success') !!}
        </div>
        @endif
        @if (\Session::has('danger'))
        <div class="alert alert-danger" style="text-align: center;">
            {!! \Session::get('danger') !!}
        </div>
        @endif
        @if ($errors->address->first('address'))
        <div class="alert alert-danger" style="text-align: center;">
            {{ $errors->address->first('address') }}
        </div>
        @endif
        <button type="button" class="btn" data-toggle="modal" data-target="#addAddress" data-whatever="@addAddress">Thêm địa chỉ</button>
        <div class="row">
            @if (count($addressdata) == 0)
            <p>Không tìm thấy địa chỉ</p>
            @else
            @foreach ($addressdata as $address)
            <div class="col-lg-4 mt-5">
                <div class="order-box">
                    <div class="order-box-no">
                        @if($address->address_type == 1)
                        Nhà riêng
                        @elseif($address->address_type == 2)
                        Nơi làm việc
                        @elseif($address->address_type == 3)
                        Khác
                        @endif
                        <h4>{{$address->address}}</h4>
                        <span class="order-status">Địa chỉ : <span>{{$address->landmark}}</span></span><br>
                        <span class="order-status">Số nhà : <span>{{$address->building}}</span></span><br>
                        <span class="order-status">Mã vùng : <span>{{$address->pincode}}</span></span>
                    </div>
                    <div class="order-box-price">
                        <h6><a href="#" onclick="GetData('{{$address->id}}')"> Sửa </a></h6>
                        <h6><a href="#" onclick="DeleteAddress('{{$address->id}}','{{Session::get('id')}}')"> Xóa </a></h6>
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>
        {!! $addressdata->links() !!}
    </div>
</section>
<!-- Add Address -->
<div class="modal fade" id="addAddress" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Thêm địa chỉ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add_address" method="post" action="{{ URL::to('/user/addaddress') }}">
                <div class="modal-body">
                    @csrf
                    <label>Loại địa chỉ</label>
                    <div class="form-group">
                        <select class="form-control" name="address_type" id="address_type">
                            <option value="">Chọn loại địa chỉ</option>
                            <option value="1">Nhà riêng</option>
                            <option value="2">Nơi làm việc</option>
                            <option value="3">Khác</option>
                        </select>
                    </div>
                    <label>Địa chỉ</label>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Nhập địa chỉ" name="address" id="address" autocomplete="on">
                        <input type="hidden" id="lat" name="lat" />
                        <input type="hidden" id="lang" name="lang" />
                        <input type="hidden" id="city" name="city" />
                        <input type="hidden" id="state" name="state" />
                        <input type="hidden" id="country" name="country" />
                    </div>
                    <label>Địa điểm</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="landmark" id="landmark" placeholder="Nhập tên địa điểm">
                    </div>
                    <label>Số nhà</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="building" id="building" placeholder="Nhập số nhà">
                    </div>
                    <label>Mã vùng</label>
                    <div class="form-group">
                        <select class="form-control" name="pincode" id="pincode">
                            <option value="">Chọn mã vùng</option>
                            @foreach($getpincode as $pincode)
                            <option value="{{$pincode->pincode}}">{{$pincode->pincode}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Address -->
<div class="modal fade" id="EditAddress" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelEdit" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabelEdit">Chỉnh sửa địa chỉ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit_address" method="post" action="{{ URL::to('/user/editaddress') }}">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" class="form-control" name="id" id="id">
                    <label>Loại địa chỉ</label>
                    <div class="form-group">
                        <select class="form-control" name="address_type" id="get_address_type">
                            <option value="">Chọn loại địa chỉ</option>
                            <option value="1">Nhà riêng</option>
                            <option value="2">Nơi làm việc</option>
                            <option value="3">Khác</option>
                        </select>
                    </div>
                    <label>Địa chỉ</label>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Nhập địa chỉ" name="address" id="get_address" autocomplete="on">
                        <input type="hidden" id="get_lat" name="lat" />
                        <input type="hidden" id="get_lang" name="lang" />
                        <input type="hidden" id="get_city" name="city" />
                        <input type="hidden" id="get_state" name="state" />
                        <input type="hidden" id="get_country" name="country" />
                    </div>
                    <label>Địa điểm</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="landmark" id="get_landmark" placeholder="Nhập địa điểm">
                    </div>
                    <label>Số nhà</label>
                    <div class="form-group">
                        <input type="text" class="form-control" name="building" id="get_building" placeholder="Nhập số nhà">
                    </div>
                    <label>Mã vùng</label>
                    <div class="form-group">
                        <select class="form-control" name="pincode" id="get_pincode">
                            <option value="">Chọn mã vùng</option>
                            @foreach($getpincode as $pincode)
                            <option value="{{$pincode->pincode}}">{{$pincode->pincode}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@include('front.theme.footer')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgYRuLoA_VsDsX2kgKoc3JiUnVeu085Vo&libraries=places"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
<script>
$(document).ready(function() {
    $("#add_address").validate({
        rules: {
            address_type: {
                required: true,
            },
            address: {
                required: true,
            },
            landmark: {
                required: true,
            },
            building: {
                required: true,
            },
            pincode: {
                required: true,
            },
        },

    });

    $("#edit_address").validate({
        rules: {
            address_type: {
                required: true,
            },
            address: {
                required: true,
            },
            landmark: {
                required: true,
            },
            building: {
                required: true,
            },
            pincode: {
                required: true,
            },
        },

    });
});

function initialize() {
    var input = document.getElementById('address');
    var autocomplete = new google.maps.places.Autocomplete(input);
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();

        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];

            if (addressType == "administrative_area_level_1") {
                document.getElementById("state").value = place.address_components[i].short_name;
            }

            if (addressType == "locality") {
                document.getElementById("city").value = place.address_components[i].short_name;
            }

            // for the country, get the country code (the "short name") also
            if (addressType == "country") {
                document.getElementById("country").value = place.address_components[i].short_name;
            }
        }

        document.getElementById('lat').value = place.geometry.location.lat();
        document.getElementById('lang').value = place.geometry.location.lng();
    });
}
google.maps.event.addDomListener(window, 'load', initialize);

function addinitialize() {
    var input = document.getElementById('get_address');
    var autocomplete = new google.maps.places.Autocomplete(input);
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();

        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];

            if (addressType == "administrative_area_level_1") {
                document.getElementById("get_state").value = place.address_components[i].short_name;
            }

            if (addressType == "locality") {
                document.getElementById("get_city").value = place.address_components[i].short_name;
            }

            // for the country, get the country code (the "short name") also
            if (addressType == "country") {
                document.getElementById("get_country").value = place.address_components[i].short_name;
            }
        }

        document.getElementById('get_lat').value = place.geometry.location.lat();
        document.getElementById('get_lang').value = place.geometry.location.lng();
    });
}
google.maps.event.addDomListener(window, 'load', addinitialize);

</script>
<script type="text/javascript">
function GetData(id) {
    $('#preloader').show();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ URL::to('/user/show') }}",
        data: {
            id: id
        },
        method: 'POST', //Post method,
        dataType: 'json',
        success: function(response) {
            $('#preloader').hide();
            jQuery("#EditAddress").modal('show');
            $('#id').val(response.ResponseData.id);
            $('#get_address_type').val(response.ResponseData.address_type);
            $('#get_address').val(response.ResponseData.address);
            $('#get_lat').val(response.ResponseData.lat);
            $('#get_lang').val(response.ResponseData.lang);
            $('#get_city').val(response.ResponseData.city);
            $('#get_country').val(response.ResponseData.country);
            $('#get_state').val(response.ResponseData.state);
            $('#get_landmark').val(response.ResponseData.landmark);
            $('#get_building').val(response.ResponseData.building);
            $('#get_pincode').val(response.ResponseData.pincode);
        },
        error: function(error) {
            $('#preloader').hide();
        }
    })
}

function DeleteAddress(id, user_id) {
    swal({
            title: "Bạn có muốn xóa địa chỉ này",
            type: 'error',
            showCancelButton: true,
            confirmButtonText: "Có",
            cancelButtonText: "Không"
        },
        function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ URL::to('/user/delete') }}",
                    data: {
                        id: id,
                        user_id: user_id
                    },
                    method: 'POST',
                    success: function(response) {
                        if (response == 1) {
                            location.reload();
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
};

</script>
