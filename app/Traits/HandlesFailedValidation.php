<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

trait HandlesFailedValidation
{
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();
        $camelCaseErrors = $this->convertErrorKeysToCamelCase($errors);
        
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $camelCaseErrors
        ], 422));
    }
    
    /**
     * Convert error keys from snake_case to camelCase
     *
     * @param array $errors
     * @return array
     */
    protected function convertErrorKeysToCamelCase(array $errors)
    {
        $result = [];
        
        foreach ($errors as $key => $messages) {
            // Convert the main key to camelCase
            $camelKey = Str::camel($key);
            
            // Handle nested keys in validation messages (e.g., 'ledger_account_id.required')
            $camelMessages = [];
            foreach ($messages as $message) {
                $camelMessages[] = $message;
            }
            
            $result[$camelKey] = $camelMessages;
        }
        
        return $result;
    }
}
