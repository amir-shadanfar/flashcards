<?php

namespace Tests\Feature;

use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Enum\FlashcardMenuEnum;
use App\Enum\FlashcardStatusEnum;

class StatisticsTest extends TestCase
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
    public function test_show_the_statistics_of_users_practice()
    {
        $this->artisan('flashcard:interactive')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::STATS->value, FlashcardMenuEnum::values())
            ->expectsTable(['Metrics', 'Statistics'], [
                [
                    'key' => 'Total Number Of Questions',
                    'value' => $this->flashcardRepository->getTotalCount()
                ],
                [
                    'key' => 'Answered Questions',
                    'value' => $this->flashcardRepository->getAnsweredPercentage($this->user).' %'
                ],
                [
                    'key' => 'Correctly Answered Questions',
                    'value' => $this->flashcardRepository->getCorrectlyAnsweredPercentage($this->user).' %'
                ]
            ])
            ->expectsConfirmation('Back to main manu?', 'yes')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::EXIT->value, FlashcardMenuEnum::values())
            ->assertExitCode(0);
    }
}
