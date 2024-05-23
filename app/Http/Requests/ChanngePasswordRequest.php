<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChanngePasswordRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'old_password'          =>      'required|min:6|max:30',
            'password'              =>      'required|min:8|max:30',
            're_password'           =>      'required|same:password',
        ];
    }

    public function messages()
    {
        return [
            'required'      =>  ':attribute Cannot be left blank',
            'max'           =>  ':attribute too long',
            'min'           =>  ':attribute too short',
            'same'          =>  ':attribute and password are not the same',
        ];
    }

    public function attributes()
    {
        return [
            'old_password'      =>  'Password old',
            'password'          =>  'Password',
            're_password'       =>  'Confirm password',
        ];
    }
}
