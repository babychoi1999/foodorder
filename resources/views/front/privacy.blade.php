@include('front.theme.header')
<section class="favourite">
    <div class="container">
        <h2 class="sec-head">Chính sách riêng tư</h2>
        <div class="row">
            {!!$getprivacypolicy->privacypolicy_content!!}
        </div>
    </div>
</section>
@include('front.theme.footer')
