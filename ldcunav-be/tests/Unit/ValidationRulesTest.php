<?php

namespace Tests\Unit;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreSavedLocationRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidationRulesTest extends TestCase
{
    public function test_register_request_rules_accept_valid_payload(): void
    {
        $request = new RegisterRequest();
        $validator = Validator::make([
            'fullName' => 'Juan Dela Cruz',
            'studentId' => '2024-00456',
            'email' => 'juan.delacruz@liceo.edu.ph',
            'password' => 'SecurePass123!',
        ], $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_register_request_rules_reject_invalid_email_domain(): void
    {
        $request = new RegisterRequest();
        $validator = Validator::make([
            'fullName' => 'Juan Dela Cruz',
            'studentId' => '2024-00456',
            'email' => 'juan.delacruz@gmail.com',
            'password' => 'SecurePass123!',
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_register_request_rules_enforce_password_complexity(): void
    {
        $request = new RegisterRequest();

        // Missing uppercase and numbers
        $validator = Validator::make([
            'fullName' => 'Juan Dela Cruz',
            'studentId' => '2024-00456',
            'email' => 'juan.delacruz@liceo.edu.ph',
            'password' => 'lowercaseonly',
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_login_request_rules_require_email_and_password(): void
    {
        $request = new LoginRequest();
        $validator = Validator::make([], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_store_saved_location_rules_require_integer_location_id(): void
    {
        $request = new StoreSavedLocationRequest();
        $validator = Validator::make(['locationId' => 'not-an-int'], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('locationId', $validator->errors()->toArray());
    }

    public function test_update_profile_rules_require_full_name(): void
    {
        $request = new UpdateProfileRequest();
        $validator = Validator::make(['fullName' => ''], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('fullName', $validator->errors()->toArray());
    }

    public function test_forgot_password_rules_require_valid_email(): void
    {
        $request = new ForgotPasswordRequest();
        $validator = Validator::make(['email' => 'invalid-email'], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }
}
