<?php

declare(strict_types=1);

namespace ChrisReedIO\Socialment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpaLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns'],
            'password' => ['required', 'string'],
        ];
    }
}
