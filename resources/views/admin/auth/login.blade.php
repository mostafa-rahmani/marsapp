@include('admin.layouts.header')
<div style="min-height: 70vh" class="container d-flex justify-content-center flex-column">
   <div class="jumbotron bg-white">
       <h4>اپلیکیشن پرتقال</h4>
       <hr>
       <div class="row mt-4">
           <div class="col-md-4"></div>
           <div class="col-md-4">
               <form method="POST" action="/admin/login">
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
           <div class="col-md-4"></div>
       </div>
   </div>
</div>
@include('admin.layouts.footer')
