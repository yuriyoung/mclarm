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
                'name' => 'nullable|string|between:1,18',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|confirmed',
            ];
        case 'PATCH':
        case 'PUT':
            return [
                'avatar' => 'nullable|dimensions:min_width=100,min_height=200',
                'first_name' => 'nullable|string',
                'last_name' => 'nullable|string',
                'birthday' => 'date_format:Y-m-d|before:today',
            ];
        }

        return [];
    }
}
