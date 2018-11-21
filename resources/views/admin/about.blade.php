@extends('admin.layouts.main')
@section('content')
   <div class="jumbotron border-0 bg-white">
       @if($flash = session('message'))
           <p class="alert alert-info">{{$flash}}</p>
       @endif
       <h4 class="text-black-50">تنظیمات صفحه درباره ما</h4>
       <hr>
       <form class="mt-5" action="/admin/about" method="POST" enctype="multipart/form-data">
           {{ csrf_field() }}
           {{--<input type="hidden" name="_method" value="PATCH">--}}
           <div class="row">
               <div class="col-12 col-lg-5">
                   <label for="about_frist_text">درباره ما بخش بالایی</label>
                   <textarea type="text" id="about_frist_text" name="about_first_text" class="form-control text-right" rows="7">{{ $settings->about_first_text }}</textarea>
               </div>
               <div class="col-12 col-lg-7">
                   <img src="{{ $settings->about_first_img }}" class="d-block mb-3 mx-auto mr-lg-0" style="max-height: 250px;">
                   <label for="about_second_img">تصویر بخش دوم</label>
                   <input type="file" placeholder="انتخاب فایل" id="about_second_img" name="about_second_img">
               </div>
           </div>
           <div class="row mt-5">
               <div class="col-12 col-lg-5">
                   <label for="about_second_text">درباره ما بخش دوم</label>
                   <textarea type="text" id="about_second_text" name="about_second_text" class="text-right form-control" rows="7">{{ $settings->about_second_text }}</textarea>
               </div>
               <div class="col-12 col-lg-7">
                   <img src="{{ $settings->about_second_img }}" class="d-block mx-auto mb-3 mr-lg-0" style="max-height: 250px;">
                   <label for="about_first_img">تصویر بخش بالایی</label>
                   <input type="file" id="about_first_img" name="about_first_img">
               </div>
           </div>
           <button class="btn btn-lg btn-info px-5 my-3">ذخیره</button>
       </form>
   </div>
@endsection
