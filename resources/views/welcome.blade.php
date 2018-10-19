@extends('layouts.master')
@section('header_links')
  <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/app.css')  }}">
@section('navbar')
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">

            <h1><a class="navbar-brand" href="#">پرتقال</a></h1>
            <button class="navbar-toggler border-0" type="button"
                    data-toggle="collapse"
                    data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">درباره ما</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="#">دانلود اپ</a>
                    </li>
                </ul>
            </div>
        </div>
  </nav>
@endsection
@section('content')
  <div class="container">
    
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
  </div>
@endsection
