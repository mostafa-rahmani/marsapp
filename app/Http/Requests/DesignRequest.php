<?php

namespace App\Http\Requests;

use App\Design;
use Illuminate\Foundation\Http\FormRequest;

class DesignRequest extends FormRequest
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
        'description.required'  =>  'توضیحات برای پست نمیتواند خالی باشد',
        'description.string'  =>  'توضیحات باید شامل جملات و حروف باشد',
        'is_download_allowed.required'  =>  'مشخص کنید که دانلود مجاز است یا خیر',
        'is_download_allowed.boolean'  =>  'تعیین مجاز بودن دانلود باید درست یا غلط باشد',
        'image.required'  =>  'پست باید شامل یک تصویر باشد',
        'image.image'  =>  'فایل تصویر غیر مجاز است'
      ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()){
            case 'POST':
                return [
                    'description' => 'required|string',
                    'is_download_allowed' => 'boolean|required',
                    'image' => 'required|image'
                ];
            case 'PATCH':
                return [
                    'description' => 'string',
                    'is_download_allowed' => 'boolean',
                    'image' => 'image'
                ];
        }
    }
}
