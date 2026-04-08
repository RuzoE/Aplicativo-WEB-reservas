<?php

namespace Tests\Feature\Auth;

use App\Rules\PhoneNumberByPrefix;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PhoneNumberValidationTest extends TestCase
{
    public function test_registration_page_displays_country_aware_phone_field(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSee('Número de teléfono');
        $response->assertSee('phone_country', false);
    }

    public function test_accepts_valid_colombian_phone_in_international_format(): void
    {
        request()->replace([
            'phone_country' => 'co',
        ]);

        $validator = Validator::make(
            ['phone' => '+573001234567'],
            ['phone' => ['required', new PhoneNumberByPrefix()]]
        );

        $this->assertTrue($validator->passes(), json_encode($validator->errors()->all()));
    }

    public function test_rejects_us_phone_when_it_does_not_have_ten_digits(): void
    {
        request()->replace([
            'phone_country' => 'us',
        ]);

        $validator = Validator::make(
            ['phone' => '+1202555012'],
            ['phone' => ['required', new PhoneNumberByPrefix()]]
        );

        $this->assertTrue($validator->fails());
        $this->assertStringContainsString('Estados Unidos', $validator->errors()->first('phone'));
    }
}
