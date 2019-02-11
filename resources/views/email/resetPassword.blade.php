@component('mail::message')
@component('mail::panel')
    این پیام بنا به درخواست شما برای تغییر رمز عبور ارسال شده است. شما میتوانید از طریق کلیک بر روی دکمه آبی رنگ رمز عبور خود را تغییر دهید
    @component('mail::button', ['url' => $url])
        تفییر رمز عبور
    @endcomponent
@endcomponent
@component('mail::panel')
   اگر این درخواست از جانب شما ارسال نشده نیاز به انجام هیج عملی نیست. به طور خود کار به آن رسیدگی میشود
@endcomponent
@endcomponent
