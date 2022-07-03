<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Repository\FlashcardRepository;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker;

    /**
     * @var \App\Repository\FlashcardRepository
     */
    protected FlashcardRepository $flashcardRepository;

    /**
     * @var \App\Models\User
     */
    protected User $user;
    
    /**
     * @return void
     */
    protected function initDatabase()
    {
        Artisan::call('migrate');
        Artisan::call('db:seed');
        // inject repo
        $this->flashcardRepository = app(FlashcardRepository::class);
        // login a user
        $this->user = User::where('email', 'amir@gmail.com')->first();
        $this->actingAs($this->user);
    }

    /**
     * @return void
     */
    protected function resetDatabase()
    {
        Artisan::call('migrate:reset');
    }
}
