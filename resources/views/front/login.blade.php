<!DOCTYPE html>
<html>

<head>
    <title>{{$getabout->title}}</title>
    <!-- meta tag -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">
    <!-- favicon-icon  -->
    <link rel="icon" href="images/about/{{$getabout->favicon}}" type="image/x-icon">
    <!-- font-awsome css  -->
    <link rel="stylesheet" type="text/css" href="front/css/font-awsome.css">
    <!-- fonts css -->
    <link rel="stylesheet" type="text/css" href="front/fonts/fonts.css">
    <!-- bootstrap css -->
    <link rel="stylesheet" type="text/css" href="front/css/bootstrap.min.css">
    <!-- fancybox css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
    <!-- owl.carousel css -->
    <link rel="stylesheet" type="text/css" href="front/css/owl.carousel.min.css">
    <link href="assets/plugins/sweetalert/css/sweetalert.css" rel="stylesheet">
    <!-- style css  -->
    <link rel="stylesheet" type="text/css" href="front/css/style.css">
    <!-- responsive css  -->
    <link rel="stylesheet" type="text/css" href="front/css/responsive.css">
</head>

<body>
    <div id="success-msg" class="alert alert-dismissible mt-3" style="display: none;">
        <span id="msg"></span>
    </div>
    <div id="error-msg" class="alert alert-dismissible mt-3" style="display: none;">
        <span id="ermsg"></span>
    </div>
    <section class="signup-sec">
        <img src="assets/images/bg.jpg" class="bg-img" alt="">
        <div class="container">
            <div class="signup-logo">
                <a href="{{URL::to('/')}}">
                    <img src="images/about/{{$getabout->logo}}" alt="">
                    <p>{{$getabout->short_title}}</p>
                </a>
            </div>
            <form id="login" action="{{ URL::to('/signin/login') }}" method="post">
                @csrf
                <input type="email" name="email" id="email" placeholder="Email" class="w-100" required="">
                <input type="password" name="password" id="password" placeholder="Password" class="w-100" required="">
                <button type="submit" class="btn w-100">Đăng nhập</button>
                <a href="{{ url('auth/google') }}" class="btn w-50 mt-3" style="background-color: #fff;">
                    <img src="front/images/ic_google.png" alt="">
                </a>
                <a href="{{ url('auth/facebook') }}" class="btn w-50 mt-3" style="background-color: #fff;">
                    <img src="front/images/ic_fb.png" alt="">
                </a>
                <p class="already">Bạn không có tài khoản? <a href="{{URL::to('/signup')}}">Đăng ký</a></p>
                <p class="already"><a href="{{URL::to('/forgot-password')}}">Quên mật khẩu?</a></p>
                @if (\Session::has('danger'))
                <div class="alert alert-danger w-100">
                    {!! \Session::get('danger') !!}
                </div>
                @endif
            </form>
        </div>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- bootstrap js -->
    <script src="front/js/bootstrap.bundle.js"></script>
    <!-- owl.carousel js -->
    <script src="front/js/owl.carousel.min.js"></script>
    <!-- lazyload js -->
    <script src="front/js/lazyload.js"></script>
    <!-- custom js -->
    <script src="front/js/custom.js"></script>
</body>

</html>
