<?php

namespace App\Features\AuthManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routeName = $this->route()->getName();

        if ($routeName === 'verify.email') {
            return [
                'email' => 'required|email|exists:users,email',
                'code' => 'required|string|size:5',
            ];
        }

        if ($routeName === 'verify.phone') {
            return [
                'phone_code' => 'required|string',
                'phone_number' => 'required|string',
                'code' => 'required|string|size:5',
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
