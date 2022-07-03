<?php

namespace Tests\Feature;

use App\Models\Flashcard;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Enum\FlashcardMenuEnum;
use App\Enum\FlashcardStatusEnum;

class PracticeTest extends TestCase
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
    public function test_show_the_progress_table()
    {
        if ($corrects = $this->user->flashcards()->wherePivot('status', FlashcardStatusEnum::CORRECT->value)->count()) {
            $correctPercentage = round($corrects / $this->getTotalCount() * 100, 2);
        } else {
            $correctPercentage = 0.0;
        }

        $this->artisan('flashcard:interactive')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::PRACTICE->value, FlashcardMenuEnum::values())
            ->expectsTable(['#', 'Questions', 'Status'], Flashcard::all(['id', 'question', 'status'])->toArray())
            ->expectsOutput($correctPercentage . ' % Completed.(Correct questions percentage)')
            ->expectsQuestion("Pick a question to practice or press 'q' back the main menu:", 'q')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::EXIT->value, FlashcardMenuEnum::values())
            ->assertExitCode(0);
    }

    /**
     * @return void
     */
    public function test_exit_the_practice_menu_and_return_to_main_menu_if_entered_q()
    {
        $this->artisan('flashcard:interactive')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::PRACTICE->value, FlashcardMenuEnum::values())
            ->expectsQuestion("Pick a question to practice or press 'q' back the main menu:", 'q')
            ->expectsQuestion('Please choose your action?', FlashcardMenuEnum::EXIT->value)
            ->assertExitCode(0);
    }

    /**
     * @return void
     */
    public function test_show_user_an_error_message_if_the_question_has_been_answered_before()
    {
        $flashcard = Flashcard::factory()->create();

        $this->user->flashcards()->attach($flashcard, ['status' => "Correct"]);

        $this->artisan('flashcard:interactive')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::PRACTICE->value, FlashcardMenuEnum::values())
            ->expectsQuestion("Pick a question to practice or press 'q' back the main menu:", 'q')
            ->expectsOutput('You already answered that question! Try another one.')
            ->expectsQuestion("Pick a question to practice or press 'q' back the main menu:", 'q')
            ->expectsQuestion('Please choose your action?', FlashcardMenuEnum::EXIT->value)
            ->assertExitCode(0);
    }

    /**
     * @return void
     */
    public function test_ask_user_to_answer_the_question_if_question_has_not_been_answered_correctly_before()
    {
        $flashcard = Flashcard::factory()->create();

        $this->artisan('flashcard:interactive')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::PRACTICE->value, FlashcardMenuEnum::values())
            ->expectsQuestion("Pick a question to practice or press 'q' back the main menu:", 'q')
            ->expectsQuestion('Write your answer:', 'does not matter!')
            ->expectsQuestion("Pick a question to practice or press 'q' back the main menu:", 'q')
            ->expectsQuestion('Please choose your action?', FlashcardMenuEnum::EXIT->value)
            ->assertExitCode(0);
    }

    /**
     * @return void
     */
    public function test_show_user_an_error_message_if_given_answer_is_wrong()
    {
        $flashcard = Flashcard::factory()->create(['answer' => "Correct Answer",]);

        $this->artisan('flashcard:interactive')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::PRACTICE->value, FlashcardMenuEnum::values())
            ->expectsQuestion("Pick a question to practice or press 'q' back the main menu:", 'q')
            ->expectsQuestion('Write your answer:', 'Wrong answer')
            ->expectsOutput('0 % Completed.(Correct questions percentage)')
            ->expectsOutput('Incorrect Answer.')
            ->expectsQuestion("Pick a question to practice or press 'q' back the main menu:", 'q')
            ->expectsQuestion('Please choose your action?', FlashcardMenuEnum::EXIT->value)
            ->assertExitCode(0);

        $flashcard->refresh();

        $flashcardStatus = $this->user->flashcards()->wherePivot('flashcard_id', $flashcard->id)->first()->pivot->status;
        $this->assertEquals(FlashcardStatusEnum::INCORRECT->value, $flashcardStatus);
    }

    /**
     * @return void
     */
    public function test_show_user_a_success_message_if_given_answer_is_correct()
    {
        $flashcard = Flashcard::factory()->create(['answer' => "Correct Answer",]);

        $this->artisan('flashcard:interactive')
            ->expectsChoice('Please choose your action?', FlashcardMenuEnum::PRACTICE->value, FlashcardMenuEnum::values())
            ->expectsQuestion("Pick a question to practice or press 'q' back the main menu:", 'q')
            ->expectsQuestion('Write your answer:', 'Correct Answer')
            ->expectsOutput('100 % Completed.(Correct questions percentage)')
            ->expectsOutput('Correct Answer.')
            ->expectsQuestion("Pick a question to practice or press 'q' back the main menu:", 'q')
            ->expectsQuestion('Please choose your action?', FlashcardMenuEnum::EXIT->value)
            ->assertExitCode(0);

        $flashcard->refresh();

        $flashcardStatus = $this->user->cards()->wherePivot('flashcard_id', $flashcard->id)->first()->pivot->status;
        $this->assertEquals('Correct', $flashcardStatus);
    }
}
