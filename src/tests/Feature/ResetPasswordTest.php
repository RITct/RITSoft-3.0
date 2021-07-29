<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordTest extends TestCase
{
    // Test whether forgot password page is available
    public function testForgotPasswordPage()
    {
        $this->get(route('password.request'))
        ->assertSuccessful();
    }

    // Test sending password reset email
    public function testForgotPasswordEmail()
    {
        Notification::fake();

        //For each user test the reset password functionality
        $users = User::all();
        foreach ($users as $user) {
            //Test the user can request a password reset
            $this->followingRedirects()
            ->from(route('password.request'))
            ->post(route('password.email'), [
                'email' => $user->email,
            ])
            ->assertSuccessful()
            ->assertSee(__('passwords.sent'));

            //Test the email was sent to the user
            Notification::assertSentTo([$user], ResetPassword::class);
        }
    }

    // Test reset password page is available
    public function testResetPasswordPage()
    {
        //for all users test the reset password functionality
        $users = User::all();
        foreach ($users as $user) {
            $token = Password::broker()->createToken($user);
            $this->get(route('password.reset', [
                'token' => 'token',
                ]))
                ->assertSuccessful()
                ->assertSee('Change password');
        }
    }

    //Test the reset password functionality
    public function testResetPassword()
    {
        //for all users test the reset password functionality
        $users = User::all();
        foreach ($users as $user) {
            $prev_password = $user->password;
            $token = Password::broker()->createToken($user);
            $password = Str::random();

            $this->followingRedirects()
            ->from(route('password.reset', [
                'token' => $token,
                ]))
            ->post(route('password.update'), [
                'token' => $token,
                'email' => $user->email,
                'password' => $password,
                'password_confirmation' => $password,
            ])
            ->assertSuccessful()
            ->assertSee(__('passwords.reset'));
            $this->assertTrue(Hash::check($password, $user->fresh()->password));
        }
    }
}
