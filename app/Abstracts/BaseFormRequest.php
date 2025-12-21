<?php

namespace App\Abstracts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     * This method automatically normalizes all input keys to handle different formats (snake_case or camelCase)
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();
        $normalized = $this->normalizeInput($input);
        $this->replace($normalized);
    }
    
    /**
     * Recursively normalize input keys from camelCase to snake_case
     * 
     * @param array $input
     * @return array
     */
    protected function normalizeInput(array $input): array
    {
        $normalized = [];
        
        foreach ($input as $key => $value) {
            // Convert camelCase to snake_case
            $snakeKey = Str::snake($key);
            
            // If value is an array, recursively normalize its keys
            if (is_array($value)) {
                $value = $this->normalizeInput($value);
            }
            
            // Add to the normalized array
            // The snake_case version takes precedence if both exist
            if (!isset($normalized[$snakeKey])) {
                $normalized[$snakeKey] = $value;
            }
        }
        
        return $normalized;
    }
}
