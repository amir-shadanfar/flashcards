<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Enum\FlashcardMenuEnum;
use App\Enum\FlashcardStatusEnum;

class CreateFlashCardTest extends TestCase
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
    public function test_create_a_new_flash_card()
    {
        $this->artisan('flashcard:interactive')
            ->assertSuccessful()
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::CREATE->value, FlashcardMenuEnum::values())
            ->expectsQuestion('Enter the question', 'What is the most famous singer?')
            ->expectsQuestion('Enter the answer', 'Ed Sheeran')
            ->expectsOutput('New flashcard added.')
            ->expectsConfirmation('Do you want to add more?', 'no')
            ->expectsOutput('Welcome, ' . $this->user->name)
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::EXIT->value)
            ->assertExitCode(0);

        $this->assertDatabaseHas('flashcards', [
            'question' => 'What is the most famous singer?',
            'answer' => 'Ed Sheeran'
        ]);
    }
}
