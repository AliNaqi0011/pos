<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ValidationService;
use Illuminate\Validation\ValidationException;

class ValidationServiceTest extends TestCase
{
    public function test_sanitize_input_removes_html_tags()
    {
        $input = '<script>alert("xss")</script>Hello World';
        $result = ValidationService::sanitizeInput($input);
        $this->assertEquals('Hello World', $result);
    }

    public function test_validate_id_accepts_valid_integer()
    {
        $result = ValidationService::validateId('123');
        $this->assertEquals(123, $result);
    }

    public function test_validate_id_rejects_invalid_input()
    {
        $this->expectException(\InvalidArgumentException::class);
        ValidationService::validateId('abc');
    }

    public function test_validate_and_sanitize_processes_data()
    {
        $data = ['name' => '<b>Test</b>', 'email' => 'test@example.com'];
        $rules = ['name' => 'required|string', 'email' => 'required|email'];
        
        $result = ValidationService::validateAndSanitize($data, $rules);
        
        $this->assertEquals('Test', $result['name']);
        $this->assertEquals('test@example.com', $result['email']);
    }
}