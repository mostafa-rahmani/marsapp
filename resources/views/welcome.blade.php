@extends('layouts.master')
@section('header_links')
  <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/app.css')  }}">
@section('navbar')
  <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/about">درباره ما</a>
                </li>
            </ul>
            <h1 class="ml-3 my-auto"><a class="navbar-brand" href="/">اپلیکیشن مارس</a></h1>
            <img src="/svg/logo.png" alt="" style="width: 25px">
        </div>
  </nav>
@endsection
@section('content')
    <div id="landing-page" class="d-flex flex-column justify-content-center container">
        @if($flash = session('changed_pass_msg'))
            <p class="alert flash-message text-right alert-success my-2" id="flash_message">{{ $flash }}</p>
        @endif
        @if($flash = session('message'))
            <p class="alert flash-message text-right alert-success my-2" id="flash_message">{{ $flash }}</p>
        @endif
       <div class="row py-4">
           <div class="col-lg-4 text-center"></div>
           <div class="col-lg-4 text-center d-flex flex-column">
               <div class="my-0">
                   <h1 class="text-center my-5">{{ $data->landing_title }}</h1>
                   <p class="text-center">{{ $data->landing_description }}</p>
               </div>
               <a id="download-button" href="/download" class="px-5 mx-auto download-btn text-white">
                   {{ $data->button_text }}
                   <img src="/svg/logo.png" class="mx-1" alt="" >
                   <span id="download-button-span" ></span>
               </a>

           </div>
           <div class="col-lg-4 text-center"></div>
       </div>
    </div>
@endsection
