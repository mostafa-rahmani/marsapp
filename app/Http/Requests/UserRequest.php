<?php

namespace App\Http\Requests;
use App\User;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(User $user)
    {
        return true;
    }

    public function messages()
    {
      return [
        'username.string' => 'نام کاربری باید با استفاده از حروف ساخته شود',
        'username.unique'  =>  'نام کاربری توسط شخص دیگری ثبت شده است',
        'username.min'  =>  'نام کاربری نباید کم تر از 6 کاراکتر باشد',
        'profile_image.image'  =>  'فایل برای تصویر پروفایل غیر مجاز است',
        'profile_background.image'  =>  'فایل تصویر برای تصویر پس زمینه غیر مجاز است',
        'bio.string' => 'بیوگرافی باید با استفاده از حروف ساخته شود',
        'email.email'  =>  'ایمیل وافعی نیست',
        'instagram.string'  =>  'آیدی اینستاگرام شما باید غیر مجاز است و باید از حروف تشکیل شود'
      ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'string|min:6|unique:users',
            'profile_image'    => 'image',
            'bio' => 'string',
            'instagram' => 'string',
            'email' => 'email',
            'profile_background' => 'image'
        ];
    }

}
