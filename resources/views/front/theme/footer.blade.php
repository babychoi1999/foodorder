<!-- Modal Change Password-->
<div class="modal fade text-left" id="ChangePasswordModal" tabindex="-1" role="dialog" aria-labelledby="RditProduct" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modal-title text-text-bold-600" id="RditProduct">Đổi mật khẩu</label>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="errors" style="color: red;"></div>
            <form method="post" id="change_password_form">
                {{csrf_field()}}
                <div class="modal-body">
                    <label>Mật khẩu cũ </label>
                    <div class="form-group">
                        <input type="password" placeholder="Mật khẩu cũ" class="form-control" name="oldpassword" id="oldpassword">
                    </div>
                    <label>Mật khẩu mới </label>
                    <div class="form-group">
                        <input type="password" placeholder="Mật khẩu mới" class="form-control" name="newpassword" id="newpassword">
                    </div>
                    <label>Xác nhận mật khẩu </label>
                    <div class="form-group">
                        <input type="password" placeholder="Xác nhận mật khẩu" class="form-control" name="confirmpassword" id="confirmpassword">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="reset" class="btn open comman" data-dismiss="modal" value="Hủy">
                    <input type="button" class="btn open comman" onclick="changePassword()" value="Lưu">
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Add Review-->
<div class="modal fade text-left" id="AddReview" tabindex="-1" role="dialog" aria-labelledby="RditProduct" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modal-title text-text-bold-600" id="RditProduct">Thêm đánh giá</label>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="errorr" style="color: red;"></div>
            <form method="post" id="change_password_form">
                {{csrf_field()}}
                <div class="modal-body">
                    <div class="rating">
                        <input type="radio" name="rating" value="5" id="star5"><label for="star5">☆</label>
                        <input type="radio" name="rating" value="4" id="star4"><label for="star4">☆</label>
                        <input type="radio" name="rating" value="3" id="star3"><label for="star3">☆</label>
                        <input type="radio" name="rating" value="2" id="star2"><label for="star2">☆</label>
                        <input type="radio" name="rating" value="1" id="star1"><label for="star1">☆</label>
                    </div>
                    <label>Bình luận</label>
                    <div class="form-group">
                        <textarea class="form-control" name="comment" id="comment" rows="5" required=""></textarea>
                        <input type="hidden" name="user_id" id="user_id" class="form-control" value="{{Session::get('id')}}">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="reset" class="btn open comman" data-dismiss="modal" value="Hủy">
                    <input type="button" class="btn open comman" onclick="addReview()" value="Lưu">
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Add Refer-->
<div class="modal fade text-left" id="Refer" tabindex="-1" role="dialog" aria-labelledby="RditProduct" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modal-title text-text-bold-600" id="RditProduct">Chia sẻ kiếm tiền</label>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="errorr" style="color: red;"></div>
            <div class="modal-body">
                <img src='{!! asset("public/front/images/referral.png") !!}' alt="img1" border="0">
                <p style="color: #464648;font-size: 16px;font-weight: 500;margin-bottom: 0; text-align: center;">Chia sẻ mã giới thiệu cho bạn của bạn và cả hai sẽ nhận được <span style="color: #fd3b2f">{{number_format(Session::get('referral_amount'))}}{{$getdata->currency}}</span> tiền thưởng từ chương trình giới thiệu.</p>
                <hr>
                <div class="text-center mt-2">
                    <label>Mã giới thiệu </label>
                    <p style="color: #fd3b2f;font-size: 35px;font-weight: 500;margin-bottom: 0; text-align: center;">{{Session::get('referral_code')}}</p>
                </div>
                <p style="text-align: center;">-----HOẶC-----</p>
                <div class="text-center mt-2">
                    <label>Sử dụng liên kết này để chia sẻ </label>
                    <div class="form-group">
                        <input type="text" class="form-control text-center" value="{{url('/signup')}}/?referral_code={{Session::get('referral_code')}}" id="myInput" readonly="">
                        <div class="tooltip-refer">
                            <button onclick="myFunction()" class="btn btn-outline-secondary">
                                Sao chép
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<footer>
    <div class="container d-flex justify-content-between flex-wrap">
        <div class="footer-head">
            <div class="footer-logo"><img src='{!! asset("public/images/about/".$getabout->footer_logo) !!}' alt=""></div>
            <p>{!! \Illuminate\Support\Str::limit(htmlspecialchars($getabout->about_content, ENT_QUOTES, 'UTF-8'), $limit = 200, $end = '...') !!}</p>
        </div>
        <div class="footer-socialmedia">
            @if($getabout->fb != "")
            <a href="{{$getabout->fb}}" target="_blank"><i class="fab fa-facebook-f"></i></a>
            @endif
            @if($getabout->twitter != "")
            <a href="{{$getabout->twitter}}" target="_blank"><i class="fab fa-twitter"></i></a>
            @endif
            @if($getabout->insta != "")
            <a href="{{$getabout->insta}}" target="_blank"><i class="fab fa-instagram"></i></a>
            @endif
        </div>
        <div class="download-app">
            <p style="text-align: center;">Địa chỉ</p>
            <iframe style="width: 400px;height: 300px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.5486920399017!2d106.7923551138006!3d10.845808292274453!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317527158a0a5b81%3A0xf45c5d34ac580517!2zUEjDgk4gSEnhu4ZVIFRSxq_hu5xORyDEkEggR1RWVCBU4bqgSSBUUC4gSOG7kiBDSMONIE1JTkg!5e0!3m2!1sen!2s!4v1622876533154!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
    <div class="copy-right text-center">
        <a href="{{URL::to('/privacy')}}" style="color: #fff;"> Chính sách riêng tư </a>
        <p>{{$getabout->copyright}} <br> Thiết kế & Phát triển bởi <a href="https://www.facebook.com/ngoctuan99yaly" target="_blank" style="color: #000;"><b>Trần Ngọc Tuân</b>.</a></p>
    </div>
</footer>
<a onclick="topFunction()" id="myBtn" title="Go to top" style="display: block;"><i class="fad fa-long-arrow-alt-up"></i></a>
<!-- footer -->
<!-- View order btn -->
@if (Session::get('cart') && !request()->is('cart'))
<a href="{{URL::to('/cart')}}" class="view-order-btn">Xem đơn hàng</a>
@else
<a href="{{URL::to('/cart')}}" class="view-order-btn" style="display: none;">Xem đơn hàng</a>
@endif
<!-- View order btn -->
<!-- jquery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- bootstrap js -->
<script src="{!! asset('public/front/js/bootstrap.bundle.js') !!}"></script>
<!-- owl.carousel js -->
<script src="{!! asset('public/front/js/owl.carousel.min.js') !!}"></script>
<!-- lazyload js -->
<script src="{!! asset('public/front/js/lazyload.js') !!}"></script>
<!-- custom js -->
<script src="{!! asset('public/front/js/custom.js') !!}"></script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<script src="{!! asset('public/assets/plugins/sweetalert/js/sweetalert.min.js') !!}"></script>
<script src="{!! asset('public/assets/plugins/sweetalert/js/sweetalert.init.js') !!}"></script>
<script type="text/javascript">
function myFunction() {

    var copyText = document.getElementById("myInput");

    copyText.select();

    copyText.setSelectionRange(0, 99999);

    document.execCommand("copy");



    var tooltip = document.getElementById("myTooltip");

    tooltip.innerHTML = "Đã sao chép";

}



function outFunc() {

    var tooltip = document.getElementById("myTooltip");

    tooltip.innerHTML = "Sao chép link";

}



function changePassword() {

    var oldpassword = $("#oldpassword").val();

    var newpassword = $("#newpassword").val();

    var confirmpassword = $("#confirmpassword").val();

    var CSRF_TOKEN = $('input[name="_token"]').val();



    $('#preloader').show();

    $.ajax({

        headers: {

            'X-CSRF-Token': CSRF_TOKEN

        },

        url: "{{ url('/home/changePassword') }}",

        method: 'POST',

        data: { 'oldpassword': oldpassword, 'newpassword': newpassword, 'confirmpassword': confirmpassword },

        dataType: "json",

        success: function(data) {

            $("#preloader").hide();

            if (data.error.length > 0)

            {

                var error_html = '';

                for (var count = 0; count < data.error.length; count++)

                {

                    error_html += '<div class="alert alert-danger mt-1">' + data.error[count] + '</div>';

                }

                $('#errors').html(error_html);

                setTimeout(function() {

                    $('#errors').html('');

                }, 10000);

            } else

            {
                error_html += '<div class="alert alert-success mt-1">' + ' Đã thay đổi mật khẩu' + '</div>';
                $('#errors').html(error_html);

                setTimeout(function() {

                    $('#errors').html('');

                }, 10000);
                location.reload();

            }

        },
        error: function(data) {



        }

    });

}

var ratting = "";

$('.rating input').on('click', function() {

    ratting = $(this).val();

});

function addReview() {



    var comment = $("#comment").val();

    var user_id = $("#user_id").val();



    var CSRF_TOKEN = $('input[name="_token"]').val();



    // $('#preloader').show();

    $.ajax({

        headers: {

            'X-CSRF-Token': CSRF_TOKEN

        },

        url: "{{ url('/home/addreview') }}",

        method: 'POST',

        data: 'comment=' + comment + '&ratting=' + ratting + '&user_id=' + user_id,

        dataType: 'json',

        success: function(data) {

            $("#preloader").hide();

            if (data.error.length > 0)

            {

                var error_html = '';

                for (var count = 0; count < data.error.length; count++)

                {

                    error_html += '<div class="alert alert-danger mt-1">' + data.error[count] + '</div>';

                }

                $('#errorr').html(error_html);

                setTimeout(function() {

                    $('#errorr').html('');

                }, 10000);

            } else

            {

                location.reload();

            }

        },
        error: function(data) {



        }

    });

}



function contact() {

    var firstname = $("#firstname").val();

    var lastname = $("#lastname").val();

    var email = $("#email").val();

    var message = $("#message").val();

    var CSRF_TOKEN = $('input[name="_token"]').val();

    $('#preloader').show();

    $.ajax({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        },

        url: "{{ URL::to('/home/contact') }}",

        data: {

            firstname: firstname,

            lastname: lastname,

            email: email,

            message: message

        },

        method: 'POST', //Post method,

        dataType: 'json',

        success: function(response) {

            $("#preloader").hide();

            if (response.status == 1) {

                $('#msg').text(response.message);

                $('#success-msg').addClass('alert-success');

                $('#success-msg').css("display", "block");

                $("#contactform")[0].reset();

                setTimeout(function() {

                    $("#success-msg").hide();

                }, 5000);

            } else {

                $('#ermsg').text(response.message);

                $('#error-msg').addClass('alert-danger');

                $('#error-msg').css("display", "block");



                setTimeout(function() {

                    $("#error-msg").hide();

                }, 5000);

            }

        }

    })

};

function AddtoCart(id, user_id) {



    var price = $('#price').val();

    var item_notes = $('#item_notes').val();



    var addons_id = ($('.Checkbox:checked').map(function() {

        return this.value;

    }).get().join(', '));

    $('#preloader').show();

    $.ajax({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        },

        url: "{{ URL::to('/product/addtocart') }}",

        data: {

            item_id: id,

            addons_id: addons_id,

            qty: '1',

            price: price,

            item_notes: item_notes,

            user_id: user_id

        },

        method: 'POST', //Post method,

        dataType: 'json',

        success: function(response) {

            $("#preloader").hide();

            if (response.status == 1) {

                $('#cartcnt').text(response.cartcnt);

                $('#msg').text(response.message);

                $('#success-msg').addClass('alert-success');

                $('#success-msg').css("display", "block");

                $('.view-order-btn').show();



                setTimeout(function() {

                    $("#success-msg").hide();

                }, 5000);

            } else {

                $('#ermsg').text(response.message);

                $('#error-msg').addClass('alert-danger');

                $('#error-msg').css("display", "block");



                setTimeout(function() {

                    $("#success-msg").hide();

                }, 5000);

            }

        },

        error: function(error) {



            // $('#errormsg').show();

        }

    })

};

function Unfavorite(id, user_id) {

    swal({

            title: "Bạn chắc chứ?",

            text: "Bạn có muốn bỏ yêu thích sản phẩm này ?",

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

                    url: "{{ URL::to('/product/unfavorite') }}",

                    data: {

                        item_id: id,

                        user_id: user_id

                    },

                    method: 'POST',

                    success: function(response) {

                        if (response == 1) {

                            swal({

                                    title: "Thành công!",

                                    text: "Đã xóa sản phẩm khỏi mục yêu thích.",

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



function MakeFavorite(id, user_id) {

    swal({

            title: "Bạn chắc chứ?",

            text: "Bạn có muốn yêu thích sản phẩm này ?",

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

                    url: "{{ URL::to('/product/favorite') }}",

                    data: {

                        item_id: id,

                        user_id: user_id

                    },

                    method: 'POST',

                    success: function(response) {

                        if (response == 1) {

                            swal({

                                    title: "Thành công!",

                                    text: "Sản phẩm đã được đưa vào mục yêu thích.",

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

                            swal("Cancelled", "Đã xảy ra lỗi :(", "error");

                        }

                    },

                    error: function(e) {

                        swal("Cancelled", "Đã xảy ra lỗi :(", "error");

                    }

                });

            } else {

                swal("Cancelled", "", "error");

            }

        });

};



function OrderCancel(id) {

    swal({

            title: "Bạn chắc chứ?",

            text: "Bạn thực sự muốn hủy đơn hàng?",

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

                    url: "{{ URL::to('/order/ordercancel') }}",

                    data: {

                        order_id: id,

                    },

                    method: 'POST',

                    success: function(response) {

                        if (response == 1) {

                            swal({

                                    title: "Thành công!",

                                    text: "Đơn hàng đã bị hủy.",

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

};



function codeAddress() {

    $.ajax({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        },

        type: 'GET',

        url: "{{ URL::to('/cart/isopenclose') }}",

        success: function(response) {

            if (response.status == 0) {

                $('.open').hide();

                $('.openmsg').show();

            } else {

                $('.openmsg').hide();

            }

        }

    });

}

window.onload = codeAddress;

</script>
@yield('script')
</body>

</html>
