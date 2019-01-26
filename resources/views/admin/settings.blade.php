@extends('admin.layouts.main')
@section('content')
    @if($flash = session('message'))
        <p class="alert alert-info">{{$flash}}</p>
    @endif
	<div class="my-3 row">
        <div class="col-12 col-lg-6">
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
                    <div class="col">
                        <label for="button_text">متن دکمه دانلود اپ</label>
                        <input type="text" id="button_text" name="button_text"
                               value="{{ $settings->button_text }}"  class="form-control">
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
        </div>
        <div class="col-12 col-lg-6">
            <h6>لیست کاربران مدیر</h6>
            @foreach($users as $user)
                <form action="/admin/roles/manager/{{$user->id}}"  class="mt-4" method="GET">
                    {{csrf_field()}}
                    <ul class="list-unstyled">
                        @if($user->isManager())
                            <li class="alert alert-secondary p-3 text-left">
                                <span class="text-black-50 mx-3"><i class="text-danger">username: </i> {{$user->username}}</span>
                                <span class="text-black-50"><i class="text-danger">email: </i>{{ $user->email }}</span>
                                <button class="btn-danger btn-sm btn px-2 float-right" type="submit">حذف از مدیریت</button>
                            </li>
                        @endif
                    </ul>
                </form>
            @endforeach
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
    <div class="jumbotron-fluid">
        <hr>
        <div class="row">
            <div class="col-12 col-lg-6">
                <form action="/admin/settings/apk" method="POST" enctype="multipart/form-data" class="mt-5 apk_upload_form">
                    <div class="py-4">
                        <h6 class="text-right d-inline float-right">آپلود فایل اپلیکیشن</h6>
                        <button type="submit" class="btn mx-auto btn-sm float-left btn-warning px-5">ذخیره فایل</button>
                    </div>
                    <hr>
                    {{ csrf_field() }}
                    <label for="apk">انتخاب فایل اپلیکیشن</label>
                    <input type="file" name="apk" id="apk">
                </form>
                <form class="mt-5" enctype="multipart/form-data" action="/admin/footer/settings" method="POST">
                    <div class="py-4">
                        <h6 class="text-right d-inline float-right">تصاویر توسعه دهنگان</h6>
                        <button type="submit" class="btn mx-auto btn-sm float-left btn-warning px-5">ذخیره تصاویر</button>
                    </div>
                    <hr>
                    {{ csrf_field() }}
                    {{--<input type="hidden" name="_method" value="PATCH">--}}
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-none" >
                                <img src="{{ Storage::url('public/' . $settings->web_developer_img) }}" alt="" class="rounded img-thumbnail border-0"
                                     style="width: 200px; height: 200px;">
                                <label for="about_form_picture">تصویر مصطفی رحمانی</label>
                                <input type="file" name="web_developer_img" id="web_developer_img">
                                <input type="text" placeholder="لینک برای این تصویر" value="{{ $settings->web_developer_url }}" class="my-3 form-control "
                                       name="web_developer_url">
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                        <div class="card shadow-none border-0">
                            <img src="{{ Storage::url('public/' . $settings->android_developer_img) }}" alt="" class="rounded img-thumbnail border-0"
                                 style="width: 200px; height: 200px;">
                            <label for="about_form_picture">تصویر علیرضا عیسی زاده</label>
                            <input type="file" id="android_developer_img" name="android_developer_img" >
                            <input type="text" name="android_developer_url" value="{{ $settings->android_developer_url }}"
                                   placeholder="لینک برای این تصویر" class="form-control my-3">
                        </div>
                    </div>
                    </div>
                </form>
            </div>
            <div class="col-12 col-lg-6">
                <form action="/admin/footerlink" method="POST" class="mt-5">
                    {{ csrf_field() }}
                    <input name="_method" type="hidden" value="PATCH">
                    <div class="py-4">
                        <h6 class="text-right d-inline float-right">لینک های فووتر</h6>
                        <button class="btn mx-auto btn-sm float-left btn-warning px-5">ذخیره تغییرات</button>
                    </div>
                    <hr>
                    <ul class="list-group mt-3">
                        @foreach($footer_links as $item)
                            <li class="list-group-item border-0 px-0">
                                <input type="text" class="form-control d-inline float-right" style="width: 300px"
                                       name="footer_links[{{$item->id}}][footer_link]" value="{{ $item->footer_link }}">
                                <input type="hidden" name="footer_links[{{$item->id}}][id]" value="{{ $item->id }}">
                                <input type="text" class="d-inline form-control w-25 float-right" style="width: 300px;"
                                       @if($item->footer_url)
                                       placeholder="بدون آدرس"
                                       @endif
                                       name="footer_links[{{$item->id}}][footer_url]" value="{{ $item->footer_url }}">
                                <a href="/admin/footerlink/{{ $item->id }}/delete" class="btn btn-danger px-3 d-inline float-left">حذف</a>
                            </li>
                        @endforeach
                    </ul>
                </form>
                <form action="/admin/footerlink" method="POST" class="mt-3">
                    {{ csrf_field() }}

                    <h6 class="text-right">لینک جدید برای فووتر</h6>
                    <hr>
                    <div class="py-3">
                        <input type="text" class="d-inline float-right form-control"
                               style="width: 300px" placeholder="متن لینک " name="footer_link">
                        <input type="text" class="d-inline w-25 float-right form-control"
                               style="width: 300px" placeholder="آدرس برای لینک" name="footer_url">
                        <button class="btn btn-info px-3 float-left" type="submit">اضافه کن</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
