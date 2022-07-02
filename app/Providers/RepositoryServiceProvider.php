<?php

namespace App\Providers;

use App\Repository\FlashcardRepositoryInterface;
use App\Repository\FlashcardRepository;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FlashcardRepositoryInterface::class, FlashcardRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
