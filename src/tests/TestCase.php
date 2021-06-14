<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public static $defaultPassword = "123456";

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
    }
}
