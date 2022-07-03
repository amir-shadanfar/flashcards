<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Enum\FlashcardMenuEnum;
use App\Enum\FlashcardStatusEnum;

class MainMenuTest extends TestCase
{
    
    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();
    }

    public function tearDown(): void
    {
        $this->resetDatabase();
        parent::tearDown();
    }

    /**
     * @return void
     */
    public function test_exit_application_when_chosen_exit_option()
    {
        $this->artisan('flashcard:interactive')
            ->assertSuccessful()
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::EXIT->value, FlashcardMenuEnum::values())
            ->assertExitCode(0);
    }
}
