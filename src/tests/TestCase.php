<?php

namespace Tests;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use WithFaker;
    use RefreshDatabase;

    protected $seed = true;

    public static string $defaultPassword = "123456";
    public array $users;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
        $this->setUpUsers();
    }

    public function pickRandomUser($role = null)
    {
        if ($role) {
            return $this->users[$role][array_rand($this->users[$role])];
        }
        $random_role = $this->users[array_rand($this->users)];
        return $random_role[array_rand($random_role)];
    }

    private function getHighestRole($roles)
    {
        $role_names = array_map(function ($role) {
            return $role["name"];
        }, $roles->toArray());

        if (in_array(Roles::PRINCIPAL, $role_names)) {
            return Roles::PRINCIPAL;
        } elseif (in_array(Roles::HOD, $role_names)) {
            return Roles::HOD;
        } elseif (in_array(Roles::STAFF_ADVISOR, $role_names)) {
            return Roles::STAFF_ADVISOR;
        } else {
            // Faculty, Student
            return array_pop($role_names);
        }
    }

    public function setUpUsers()
    {
        // Arrange users according to their roles
        $this->users = array();
        $_users = User::all();
        foreach ($_users as $user) {
            $roles = $user->roles()->get();

            $role = $this->getHighestRole($roles);

            if (!key_exists($role, $this->users)) {
                $this->users[$role] = array();
            }
            array_push($this->users[$role], $user);
        }
    }

    public function assertUsersOnEndpoint(
        string $url,
        string $method,
        array $user_status_map,
        array $data = array()
    ): void {
        // Function to assert level permissions on endpoints
        foreach ($user_status_map as $role => $status) {
            $response = call_user_func(
                array($this->actingAs($this->pickRandomUser($role)), $method),
                $url,
                $data
            );
            $response->assertStatus($status);
        }
    }

    public function assertLoginRequired($url, $method = "get")
    {
        Auth::logout();
        call_user_func([$this, $method], $url)->assertRedirect(route("login"));
    }
}
