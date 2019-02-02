@extends('layouts.master')
@section('header_links')
  <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
    <title>{{ $data->landing_title }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css')  }}">
    
@section('navbar')
  <nav class="navbar navbar-expand-lg navbar-light navbar-home">
        <div class="container-fluid  wrapper pr-xl-4">
            <ul class="navbar-nav ml-auto py-4">
                <li class="nav-item">
                    <a href="/about">درباره  اپ</a>
                </li>
            </ul>
             <h1 class="ml-4 my-auto"><a href="/" class="mr-2"> مارس</a></h1>
            <img src="/svg/logo-circle.svg" alt="" style="width: 25px">
        </div>
    </nav>
@endsection
@section('content')
    <div id="landing-page" style="overflow: hidden;" class="d-flex flex-column justify-content-center container">
        @if($flash = session('changed_pass_msg'))
            <p class="alert flash-message text-right alert-success my-2" id="flash_message">{{ $flash }}</p>
        @endif
        @if($flash = session('message'))
            <p class="alert flash-message text-right alert-success my-2" id="flash_message">{{ $flash }}</p>
        @endif
       <div class="row py-4 mb-auto mt-3 my-lg-auto px-lg-0">
          <img src="/svg/mobile.svg" alt="" id="app-view">
           <div class="col-lg-8 text-center"></div>
           <div class="col-lg-4 text-center d-flex flex-column">
               <div class="my-0">
                   <h1 class="text-right mb-xl-4">{{ $data->landing_title }}</h1>
                   <p class="text-right">{{ $data->landing_description }}</p>
               </div>
               <a href="/download" role="button" class="py-3 mt-4 px-4 ml-auto button text-white">
                    <span>{{ $data->button_text }}</span>
                  <i class="fas fa-cloud-download-alt"></i>
               </a>
            
           </div>
       </div>
    </div>
@endsection
