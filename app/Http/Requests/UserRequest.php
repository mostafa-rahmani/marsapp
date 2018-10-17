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


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'String|min:3|unique:users',
            'profile_image_url'    => 'image',
            'bio' => 'String',
            'instagram' => 'String',
            'email' => 'email',
            'profile_background' => 'required'
        ];
    }

}
