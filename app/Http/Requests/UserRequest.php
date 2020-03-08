<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        switch ($this->getMethod()) {
        case 'POST':
            return [
                'name' => 'required|unique:users,name|between:1,18',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|confirmed',
            ];
        case 'PATCH':
        case 'PUT':
            return [
                'name' => 'sometimes|unique:users,name|between:1,18',
                'avatar' => 'nullable|dimensions:min_width=100,min_height=200',
                'birthday' => 'date_format:Y-m-d|before:today',
            ];
        }

        return [];
    }
}
