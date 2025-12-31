<?php

namespace App\Features\SystemManagements\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingCenterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $centerId = $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:training_centers,code,' . $centerId,
            'governorate_id' => 'required|exists:governorates,id',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
