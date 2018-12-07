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
