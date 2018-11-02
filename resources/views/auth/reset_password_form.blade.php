@extends('layouts.master')
@section('header_links')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection
@section('content')
   <div class="jumbotron m-lg-5 mb-lg-0 p-lg-4 bg-white">
       <div class="row my-md-5">
           <div class="col-md-3"></div>
           <div class="col-md-6">
               <form action="/reset" id="reset_pass_form" class="p-md-5 d-flex flex-column justify-content-center text-right" method="POST">
                   @if($errors->all())
                       <p class="alert alert-danger">خطا در ورودی: رمز عبور باید حداقل هشت کاراکتر باشد. در یکسان بودن تایید رمز عبور نیز دقت کنید. </p>
                   @endif
                   <h4 class="text-right my-4">رمز عبور خود را از طریق فرم زیر تغییر دهید</h4>
                   {{ csrf_field() }}
                   <input type="hidden" value="{{ $passwordReset->token }}" name="token">
                   <input type="hidden" value="  {{ $passwordReset->email }} " name="email">
                   <div class="form-group">
                       <label for="exampleInputPassword1" class="float-right">رمز عبور جدید</label>
                       <input type="password" name="password" class="form-control" id="exampleInputPassword1" >
                   </div>
                   <div class="form-group">
                       <label for="password_confirmation" class="float-right">تایید دوباره رمز عبور</label>
                       <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                   </div>
                   <button type="submit" class="mx-auto my-3 d-block w-100 btn btn-secondary ">ذخیره</button>
               </form>
           </div>
           <div class="col-md-3"></div>
       </div>
   </div>
@endsection
