<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenggunaRequest extends FormRequest
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
        if (request()->isMethod('post')) {
            $passwordRule = 'required';
            $confirmationPasswordRule = 'required';
        } elseif (request()->isMethod('put')) {
            $passwordRule = 'sometimes';
            $confirmationPasswordRule = 'sometimes';
        }
        return [
            'username' => [Rule::unique('pengguna1')->ignore(request()->segment(3))],
            'nama' => [Rule::unique('pengguna1')->ignore(request()->segment(3))],
            'password' => [$passwordRule],
            'confirmation_password' => [$confirmationPasswordRule, 'same:password'],
        ];
    }

    public function messages()
    {
        return [
            'username.unique'    => 'Username telah ada!',
            'nama.unique'    => 'Nama telah ada!',
            'password.required'    => 'Password harus diisi!',
            'confirmation_password.required'    => 'Konfirmasi password harus diisi!',
        ];
    }
}
