<?php

namespace App\Http\Requests\API;

class ForgotPasswordRequest extends ApiRequest
{
    public function rules()
    {
        return [
            'email'        => 'required|email|exists:users,email',
        ];
    }
}
