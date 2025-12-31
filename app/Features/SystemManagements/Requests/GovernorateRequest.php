<?php

namespace App\Features\SystemManagements\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GovernorateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $governorateId = $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:governorates,code,' . $governorateId,
            'is_active' => 'sometimes|boolean',
        ];
    }
}
