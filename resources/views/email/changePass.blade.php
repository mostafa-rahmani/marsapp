<!DOCTYPE html>
<html lang="en">
<head>
    <title>تغییر رمز عبور</title>
 <link rel="stylesheet" href="{{ asset('css/email.css') }}">
</head>
<body  >
    <div class="alert alert-info">

    <p>نام کاربری :
        {{$username}}
        </p>
    <p>این ایمیل بنا به درخواست شما برای تغییر رمز عبور ارسال شده است. میتوانید از طریق لینک زیر اقدام به تغییر رمز عبور خود نمایید</p>
    <a href="{{$resetUrl}}">تغییر رمز عبور</a>
    </div>
    <p class="alert alert-warning">چنانچه این درخواست از جانب شما نبوده هیچ نیاز به هیچ اقدامی نیست و به طور خودکار به آن رسیدگی میشود.</p>
</body>
</html>
