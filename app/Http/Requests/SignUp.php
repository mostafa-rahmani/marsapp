<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignUp extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(){
      return [
        'email.required' => 'ایمیل نمیتواند خالی باشد',
        'email.email'  =>  'ایمیل وافعی نیست',
        'email.unique'  =>  'این ایمیل توسط کاربر دیگری ثبت شده است',
        'username.required' => 'نام کاربری نمیتواند خالی باشد',
        'username.unique'  =>  'نام کاربری توسط شخص دیگری ثبت شده است',
        'username.min'  =>  'نام کاربری نباید کم تر از 6 کاراکتر باشد',
        'username.string' => 'نام کاربری باید با استفاده از حروف ساخته شود',
        'password.required'  =>  'رمز عبور نمیتواند خالی باشد',
        'password.confirmed'  =>  'تایید رمز عبور با رمز عبور همخوانی ندارد',
        'password.min'  =>  'رمز عبور باید حداقل هشت کاراکتر باشد'
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
          'username'   => 'bail|required|min:6|unique:users|string',
          'email'      => 'bail|required|email|unique:users',
          'password'   => 'bail|required|min:8|confirmed'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
      if($validator->fails()){
        return 'fails';
      }
    }
}
