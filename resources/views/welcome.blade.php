@extends('layouts.master')
@section('header_links')
  <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/app.css')  }}">
@section('navbar')
  <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
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
                        <a class="nav-link" href="http://{{ $data->app_download_url }}">دانلود اپ</a>
                    </li>
                </ul>
                <h1 class="ml-3 my-auto"><a class="navbar-brand" href="/">پرتقال</a></h1>
            </div>
        </div>
  </nav>
@endsection
@section('content')
  <div class="container">
    <div id="landing-page" class="d-flex flex-column justify-content-center">
        @if($flash = session('changed_pass_msg'))
            <p class="alert flash-message text-right alert-success my-2" id="flash_message">{{ $flash }}</p>
        @endif
        @if($flash = session('message'))
            <p class="alert flash-message text-right alert-success my-2" id="flash_message">{{ $flash }}</p>
        @endif
       <div class="row py-4">
           <div class="col-lg-3 text-center"></div>
           <div class="col-lg-6 text-center d-flex flex-column">
               <div class="my-5">
                   <h1 class="text-center my-5">{{ $data->landing_title }}</h1>
                   <p class="text-center">{{ $data->landing_description }}</p>
                   <a href="http://{{ $data->app_download_url }}" class="px-5 mx-auto download-btn text-white">دانلود اپ اندروید</a>
               </div>
           </div>
           <div class="col-lg-3 text-center"></div>
       </div>
    </div>
  </div>
@endsection
