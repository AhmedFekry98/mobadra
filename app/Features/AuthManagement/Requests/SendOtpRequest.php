<?php

namespace App\Features\AuthManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routeName = $this->route()->getName();

        if ($routeName === 'verify.email.send') {
            return [
                'email' => 'required|email|exists:users,email',
            ];
        }

        if ($routeName === 'verify.phone.send') {
            return [
                'phone_code' => 'required|string',
                'phone_number' => 'required|string',
            ];
        }

        return [];
    }

    /**
     * Get full phone number (phone_code + phone)
     */
    public function getFullPhone(): ?string
    {
        if ($this->phone_number && $this->phone_code) {
            return $this->phone_code . $this->phone_number;
        }
        return null;
    }
}
