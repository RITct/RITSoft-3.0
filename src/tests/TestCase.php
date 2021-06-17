<?php

namespace Tests;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public static $defaultPassword = "123456";
    public $users;

    protected $seed = true;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
        $this->setUpUsers();
    }

    public function pickRandomUser($role=null){
        if($role)
            return $this->users[$role][array_rand($this->users[$role])];
        $random_role = $this->users[array_rand($this->users)];
        return $random_role[array_rand($random_role)];
    }

    public function setUpUsers()
    {
        // Arrange users according to their roles
        $this->users = array();
        $_users = User::all();
        foreach ($_users as $user) {
            $roles = $user->roles()->get();
            foreach ($roles as $role) {
                if (!key_exists($role->name, $this->users)) {
                    $this->users[$role->name] = array();
                }
                array_push($this->users[$role->name], $user);
            }
        }
    }

    public function assertUsersOnEndpoint(string $url, string $method, array $user_status_map, array $data=array()) : void
    {
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
}
