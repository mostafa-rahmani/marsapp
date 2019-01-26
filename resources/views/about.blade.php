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
                    <a class="nav-link active" href="/about">درباره ما</a>
                </li>
            </ul>
            <h1 class="ml-3 my-auto"><a class="navbar-brand" href="/">اپلیکیشن مارس</a></h1>
        </div>
    </nav>
@endsection
@section('content')
   <div id="about-page">
    <div class="container-fluid">
        <div class="row about-item">
            <div class="col-lg-2 col-12"></div>
            <div class="col-12 col-md-6 col-lg-4 text-center text-md-left order-md-2">
                <img src="{{ Storage::url('public/' . $settings->about_first_img) }}">
            </div>
            <div class="col-12 col-md-6 col-lg-4 pt-3 order-md-1">
                <p class="text-center  text-md-right dialog-box mx-auto">{{ $settings->about_first_text }}</p>
            </div>
            <div class="col-lg-2 col-12"></div>
        </div>
        <div class="row about-item">
            <div class="col-lg-2 col-12"></div>
            <div class="col-12 col-md-6 col-lg-4 text-lg-right text-center">
                <img src="{{ Storage::url('public/' . $settings->about_second_img) }}" alt="">
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <p class="text-center text-md-right dialog-box mx-auto"> {{ $settings->about_second_text }} </p>
            </div>
            <div class="col-lg-2 col-12"></div>
        </div>
    </div>
   </div>
@endsection
@section('footer')
    <footer id="main-footer">
        <div class="row">
            <div class="col-12 col-lg-6 p-5">
                <section id="developers">
                    <h4 class="text-center my-5">اپلیکیشن مارس توسط این توسعه دهنگان خلاق ساخته شده</h4>
                    <div class="d-flex flex-column flex-md-row justify-content-center">
                        <div class="card border-0 card__one my-4 text-center mx-md-4 mx-auto">
                            <a  class="developer_img web-dev" href="{{ $settings->web_developer_url }}"
                                style="background-image: url({{ Storage::url('public/' . $settings->web_developer_img ) }})">
                            </a>
                            <div class="card-body">
                                <h5>مصطفی رحمانی </h5>
                                <p>برنامه نویس وب و سرور</p>
                            </div>
                        </div>
                        <div class="card border-0 my-4 mx-auto mx-md-4 text-center card__two">
                            <a href="{{ $settings->android_developer_url }}" class="developer_img android-dev"
                                    style="background-image: url({{ Storage::url('public/' . $settings->android_developer_img ) }})">
                            </a>
                            <div class="card-body">
                                <h5>علیرضا عیسی زاده</h5>
                                <p> برنامه نویس اندروید</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-12 col-lg-6 p-5 d-flex flex-row justify-content-center">
                <section id="developers">
                    <h4 class="text-center my-5">راه های تماس با ما</h4>
                    <ul class="list-unstyled">
                        @foreach($footer_links as $item)

                            <li class="text-center text-body">
                               @if($item->footer_url)
                                    <a class="text-body " href="{{$item->footer_url}}" >{{ $item->footer_link }}</a>
                                @else
                                   {{ $item->footer_link }}
                                @endif
                            </li>

                        @endforeach
                    </ul>
                </section>
            </div>
        </div>
    </footer>
@endsection
