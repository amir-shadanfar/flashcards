<?php

namespace App\Console\Commands\Flashcard\Handlers;

use App\Models\Flashcard;
use App\Enum\FlashcardStatusEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PracticeHandler extends Command
{
    /**
     * @var bool
     */
    protected $hidden = true;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flashcard:practice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Practice flashcards which is added before.';

    /**
     * @param \Illuminate\Console\Command $mainMenu
     */
    public function __construct(private Command $mainMenu)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $firstTime = true;
        while (true) {
            // check first time that user come to the practice part to show progress table
            if ($firstTime) {
                $this->showUserProgress();
                $firstTime = false;
            }

            // continue or stop
            $flashcardId = $this->mainMenu->ask("Pick a question to practice or press 'q' back the main menu:");
            if ($flashcardId == 'q') {
                return $this->mainMenu->handle();
            }

            // validate user input
            $validator = Validator::make([
                'id' => $flashcardId
            ], [
                'id' => 'required|numeric|exists:flashcards,id',
            ], [
                    'required' => 'Enter a question id from the table',
                ]
            );
            if ($validator->errors()->count()) {
                $this->mainMenu->error($validator->errors()->first());
                continue;
            }

            // check question is answered correctly before or not
            if ($this->mainMenu->flashcardRepository->answeredCorrectlyBefore(Auth::user(), $flashcardId)) {
                $this->mainMenu->error('You already answered that question! Try another one.');
                $this->showUserProgress();
                continue;
            }

            // control the answer
            $flashcard = $this->mainMenu->flashcardRepository->find($flashcardId);
            if ($flashcard->answer === $this->mainMenu->ask('Write your answer:')) {
                $this->mainMenu->flashcardRepository->updateAnswerStatus(Auth::user(), $flashcardId, FlashcardStatusEnum::CORRECT);
                $this->mainMenu->info('Correct Answer.');
            } else {
                $this->mainMenu->flashcardRepository->updateAnswerStatus(Auth::user(), $flashcardId, FlashcardStatusEnum::INCORRECT);
                $this->mainMenu->error('Incorrect Answer!');
            }
            $this->showUserProgress();
        }
    }

    /**
     * @return void
     */
    private function showUserProgress(): void
    {
        // table
        $this->mainMenu->table(['#', 'Questions', 'Status'], $this->mainMenu->flashcardRepository->getAllWithCurrentUserAnswerStatus(Auth::user()));
        // footer
        $this->mainMenu->line(sprintf("%s %% Completed.(Correct questions percentage)", $this->mainMenu->flashcardRepository->getCorrectlyAnsweredPercentage(Auth::user())));
    }
}
