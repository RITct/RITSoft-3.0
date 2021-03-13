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
        $loginData = ['username' => 'testuser', 'password' => '123456'];

        $user = User::factory()->create([
            'username' => $loginData['username'],
            'password' => bcrypt($loginData['password'])
        ]);

        $this->json('POST', 'auth/login/', $loginData, ['Accept' => 'application/json'])
            ->assertRedirect("/");

        $this->assertAuthenticated();
    }

    public function testLoginFail()
    {
        $loginData = ['username' => 'testuser', 'password' => '123456'];

        $this->json('POST', 'auth/login/', $loginData, ['Accept' => 'application/json']);

        $this->assertGuest();
    }
}
