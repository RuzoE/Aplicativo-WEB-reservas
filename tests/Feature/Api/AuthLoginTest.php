<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_iniciar_sesion_con_credenciales_validas(): void
    {
        $user = User::factory()->create([
            'name' => 'Recepción Demo',
            'email' => 'recepcion@gmail.com',
            'password' => 'PasswordSeguro123!',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'recepcion@gmail.com',
            'password' => 'PasswordSeguro123!',
            'device_name' => 'postman',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Autenticación exitosa.')
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonStructure([
                'success',
                'message',
                'token_type',
                'token',
                'user' => ['id', 'name', 'email'],
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    /** @test */
    public function devuelve_422_cuando_las_credenciales_son_invalidas(): void
    {
        User::factory()->create([
            'name' => 'Recepción Demo',
            'email' => 'recepcion@gmail.com',
            'password' => 'PasswordSeguro123!',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'recepcion@gmail.com',
            'password' => 'PasswordInvalido123!',
            'device_name' => 'postman',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => __('auth.failed'),
            ]);
    }
}
