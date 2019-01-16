@include('admin.layouts.header')
<div id="login_page_wrapper" class="d-flex  justify-content-center flex-column">
    <div class="row">
        <div class="col-12 col-md-3"></div>
        <div class="col-12 col-md-6">
            <div class="jumbotron bg-white">
                <h4>
                    <a href="/">خانه</a> >
                    ورود
                </h4>
                <hr>
                <div class="mt-4 p-md-5">
                    <form method="POST" action="/auth/login">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="login_email">ایمیل</label>
                                <input type="email" class="form-control"
                                       id="login_email" name="email"
                                       aria-describedby="emailHelp" placeholder="آدرس ایمیل شما">
                            </div>
                            <div class="form-group">
                                <label for="login_password">رمز عبور</label>
                                <input type="password" class="form-control"  id="login_password" name="password" placeholder="رمز عبور شما">
                            </div>
                            <div class="form-group form-check px-3">
                                <input type="checkbox" name="remember_me" class="form-check-input" value="true" id="remember_me">
                                <label class="form-check-label" for="remember_me">مرا به خاطر بسپار</label>
                            </div>
                            <button type="submit" class="btn btn-outline-info w-100 d-block px-5">ورود</button>
                        </form>
                </div>
            </div>
    </div>

</div>
@include('admin.layouts.footer')
