<?php

namespace Tests\Feature;

use App\Models\Flashcard;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Enum\FlashcardMenuEnum;
use App\Enum\FlashcardStatusEnum;

class ListAllFlashCardsTest extends TestCase
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
    public function test_get_all_flash_cards()
    {
        $this->artisan('flashcard:interactive')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::LIST->value, FlashcardMenuEnum::values())
            ->expectsTable(['#', 'Questions', 'Answers'], $this->flashcardRepository->list(['id', 'question', 'answer'])->toArray())
            ->expectsConfirmation('Back to main manu?', 'yes')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::EXIT->value, FlashcardMenuEnum::values())
            ->assertExitCode(0);
    }
}
