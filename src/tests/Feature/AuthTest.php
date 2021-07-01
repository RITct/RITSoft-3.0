<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function testLoginOk()
    {
        $loginData = ["username" => "blah@rit.com", "password" => TestCase::$defaultPassword];
        User::factory()->create($loginData);

        $this->post(route("login.post"), $loginData, ["HTTP_REFERER" => route("login.post")])
            ->assertRedirect("/");

        $this->assertAuthenticated();
    }

    public function testLoginFail()
    {
        $loginData = ['username' => 'testuser', 'password' => '123456'];
        $this->post(route("login.post"), $loginData, ["HTTP_REFERER" => route("login.post")])
            ->assertRedirect(route("login.post"));

        $this->assertGuest();
    }
}
