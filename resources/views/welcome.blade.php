@extends('layouts.master')
@section('content')
    @if($flash = session('changed_pass_msg'))
        <p class="alert alert-success" id="flash_message">{{ $flash }}</p>
    @endif

    <div id="landing-page" class="d-flex flex-column justify-content-center">
       <div class="row">
           <div class="col-md-3"></div>
           <div class="col-md-6 text-center">
               <h1 class="text-center my-5">پرتقال دنیایی برای شکوفا شدن</h1>
               <p class="text-center">طراح هستید یا نقاش و یا شاید عکاس، اپ پرتقال رو دانلود کنید با جامعه ای بزرگ از ایده ها و تفکرات دیگر متصل شوید. کار های خود را به اشتراک بگذارید و محبوب شوید</p>
               <a href="" class="btn px-5 btn-info text-white">دانلود اپ اندروید</a>

           </div>
           <div class="col-md-3"></div>
       </div>
    </div>
@endsection
