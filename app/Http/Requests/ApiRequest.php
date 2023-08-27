<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ApiRequest extends FormRequest
{
    public function rules()
    {
        return call_user_func([
            $this,
            "rules_v{$this->header('Api-Version')}"
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException(
            $validator,
            response()->preferredFormat([
                'errors' => $validator->errors(),
            ], 400)
        );
    }
}
