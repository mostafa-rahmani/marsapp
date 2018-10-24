@extends('admin.layouts.main')
@section('content')
    @if($flash = session('message'))
        <p class="alert alert-info">{{$flash}}</p>
    @endif
	<div class="my-3">
        <form action="/admin/settings" class="mb-4" method="POST">
            @if($errors)
                <ul class="list-unstyled">
                    @foreach($errors->all() as $error)
                        <li class="alert alert-danger">{{$error}}</li>
                    @endforeach
                </ul>
            @endif
            {{ csrf_field() }}
            <div class="row form-group">
                <div class="col">
                    <label for="app_download_url">لینک دانلود اپلیکیشن</label>
                    <input type="text" id="app_download_url" value="{{$settings->app_download_url}}" class="form-control" name="app_download_url">
                </div>
                <div class="col">
                    <label for="landing_title">عنوان صفحه اصلی</label>
                    <input type="text" id="landing_title" name="landing_title" value="{{ $settings->landing_title }}"  class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label for="landing_description">متن صفحه اصلی وبسایت</label>
                <textarea name="landing_description" id="landing_description"  rows="5" class="form-control">{{ $settings->landing_description }}</textarea>
            </div>
            <div class="form-group d-inline-block form-check px-3">
                <label class="form-check-label" for="admin_register_on">ثبت نام در سایت</label>
                <span class="mx-4">
                    <input type="radio" name="admin_register_on"
                           @if($settings->admin_register_on)
                                checked
                           @endif
                           value="1" class="form-check-input" id="admin_register_on1">
                    <label class="form-check-label"  for="admin_register_on1">باز باشد</label>
                </span>
                <span class="mx-4">
                    <input type="radio" name="admin_register_on"
                           @if(!$settings->admin_register_on)
                                checked
                           @endif
                           value="0" class="form-check-input" id="admin_register_on0">
                    <label class="form-check-label" for="admin_register_on0">بسته شود</label>
                </span>
            </div>
            <button type="submit" class="btn float-left btn-info px-5 text-white">ذخیره تنظیمات</button>
        </form>
        <hr>
            <div class="row form-group">
                <div class="col-4">
                    <h6>لیست کاربران مدیر</h6>
                    @foreach($users as $user)
                    <form action="/admin/roles/manager/{{$user->id}}"  class="mt-4" method="GET">
                        {{csrf_field()}}
                        <ul class="list-unstyled">
                                @if($user->isManager())
                                    <li class="alert alert-secondary p-3">{{$user->username}}
                                        <button class="btn-danger btn-sm btn px-2 float-left" type="submit">X</button>
                                    </li>
                                @endif
                        </ul>
                    </form>
                    @endforeach
                </div>
                <div class="col-8">
                    <form action="/admin/roles/manager" method="POST">
                        {{csrf_field()}}
                        <p>مدیر اضافه کنید</p>
                        <div class="row">
                            <div class="col">
                                <input type="text" name="username" class="d-inline-block w-75 form-control" placeholder="نام کاربر را وارد کنید">
                                <button class="btn btn-info float-left px-5 text-white d-inline-block">ذخیره</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
	</div>
@endsection
