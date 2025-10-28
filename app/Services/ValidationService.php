<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidationService
{
    public static function sanitizeInput(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    public static function validateAndSanitize(array $data, array $rules): array
    {
        $validator = Validator::make($data, $rules);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        $validated = $validator->validated();
        
        // Sanitize string inputs
        foreach ($validated as $key => $value) {
            if (is_string($value)) {
                $validated[$key] = self::sanitizeInput($value);
            }
        }
        
        return $validated;
    }

    public static function validateId($id): int
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new \InvalidArgumentException('Invalid ID provided');
        }
        return (int) $id;
    }
}