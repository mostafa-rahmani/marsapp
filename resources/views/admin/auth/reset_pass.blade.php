@include('admin.layouts.header')
<div style="min-height: 70vh" class="container d-flex justify-content-center flex-column">
    <div class="jumbotron bg-white">
        <h4>اپلیکیشن پرتقال</h4>
        <hr>
        <div class="row mt-4">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <form method="POST" action="/admin/password/change">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="login_password">رمز عبور</label>
                        <input type="password" class="form-control"  id="login_password" name="password" placeholder="رمز عبور شما">
                    </div>
                    <div class="form-group">
                        <label for="new_password">رمز عبور جدید</label>
                        <input type="password" class="form-control"  id="new_password" name="new_password" placeholder="رمز عبور جدید">
                    </div>
                    <div class="form-group">
                        <label for="confirm_new_password">رمز عبور جدید</label>
                        <input type="password" class="form-control"  id="new_password" name="new_password_confirmation" placeholder="تایید رمز عبور جدید">
                    </div>
                    <button type="submit" class="btn btn-outline-info w-100 d-block px-5">تغییر</button>
                </form>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
</div>
@include('admin.layouts.footer')
