<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginOk()
    {
        $loginData = ["username" => "admin@rit.com", "password" => TestCase::$defaultPassword];
        User::factory()->create($loginData);

        $this->post('/auth/login', $loginData, ["HTTP_REFERER" => "/auth/login"])
            ->assertRedirect("/");

        $this->assertAuthenticated();
    }

    public function testLoginFail()
    {
        $loginData = ['username' => 'testuser', 'password' => '123456'];
        $this->post('/auth/login', $loginData, ["HTTP_REFERER" => "/auth/login"])
            ->assertRedirect("/auth/login");

        $this->assertGuest();
    }
}
