
@include('admin.layouts.header')
<div style="min-height: 100vh" class="row">
    <div class="col-6">
        <div class="jumbotron p-5 bg-white" style="min-height: 100vh">
            <h4>
                <img src="/img/login.svg" class="svg-icon">
                اپلیکیشن پرتقال
            </h4>
            <hr>
            <form method="POST" action="/auth/register" class="mx-auto py-5" style="width: 400px;">
                        @include('admin.layouts.error')
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="login_email">ایمیل</label>
                            <input type="email" class="form-control"
                                   id="login_email" name="email"
                                   aria-describedby="emailHelp" placeholder="آدرس ایمیل شما">
                        </div>
                        <div class="form-group">
                            <label for="username">نام کاربری</label>
                            <input type="text" class="form-control"
                                   id="username" name="username" placeholder="یک نام کاربری وارد کنید">
                        </div>
                        <div class="form-group">
                            <label for="login_password">رمز عبور</label>
                            <input type="password" class="form-control"  id="login_password" name="password" placeholder="رمز عبور شما">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">تایید رمز عبور</label>
                            <input type="password" class="form-control"  id="password_confirmation"
                                   name="password_confirmation" placeholder="تایید رمز عبور">
                        </div>
                        <button type="submit" class="btn btn-outline-info w-100 d-block px-5">ثبت نام</button>
                    </form>
        </div>
    </div>
    <div class="col-6" style="background-image:url('/img/background.jpeg'); background-position: center center;
    -webkit-background-size: cover;background-size: cover;"></div>
</div>
@include('admin.layouts.footer')
