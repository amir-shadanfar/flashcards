<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker;
    
    /**
     * @return void
     */
    protected function initDatabase()
    {
        Artisan::call('migrate');
        Artisan::call('db:seed');
    }

    /**
     * @return void
     */
    protected function resetDatabase()
    {
        Artisan::call('migrate:reset');
    }
}
