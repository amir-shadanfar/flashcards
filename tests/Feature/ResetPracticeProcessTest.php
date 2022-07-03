<?php

namespace Tests\Feature;

use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Enum\FlashcardMenuEnum;
use App\Enum\FlashcardStatusEnum;

class ResetPracticeProcessTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->initDatabase();
        // add some practice data
        Flashcard::factory(2)->create()->each(function ($flashcard) {
            $this->user->flashcards()->syncWithoutDetaching([$flashcard->id => ['status' => FlashcardStatusEnum::CORRECT->value]]);
        });
        Flashcard::factory(2)->create()->each(function ($flashcard) {
            $this->user->flashcards()->syncWithoutDetaching([$flashcard->id => ['status' => FlashcardStatusEnum::INCORRECT->value]]);
        });
    }

    public function tearDown(): void
    {
        $this->resetDatabase();
        parent::tearDown();
    }

    /**
     * @return void
     */
    public function test_decline_reset_request_if_not_confirmed()
    {
        $this->artisan('flashcard:interactive')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::RESET->value, FlashcardMenuEnum::values())
            ->expectsConfirmation('Are you sure to "reset" practice data?', 'no')
            ->expectsOutput('Practice data has been reset!')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::EXIT->value, FlashcardMenuEnum::values())
            ->assertExitCode(0);

        $this->assertCount(2, $this->user->flashcards()->wherePivot('status', FlashcardStatusEnum::CORRECT->value)->get());
        $this->assertCount(2, $this->user->flashcards()->wherePivot('status', FlashcardStatusEnum::INCORRECT->value)->get());
    }

    /**
     * @return void
     */
    public function test_reset_practice_process_and_start_fresh_if_reset_request_confirmed()
    {
        $this->artisan('flashcard:interactive')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::RESET->value, FlashcardMenuEnum::values())
            ->expectsConfirmation('Are you sure to "reset" practice data?', 'yes')
            ->expectsOutput('Practice data has been reset!')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::EXIT->value, FlashcardMenuEnum::values())
            ->assertExitCode(0);

        $this->assertCount(0, $this->user->flashcards()->wherePivot('status', FlashcardStatusEnum::CORRECT->value)->get());
        $this->assertCount(0, $this->user->flashcards()->wherePivot('status', FlashcardStatusEnum::INCORRECT->value)->get());
    }
}
