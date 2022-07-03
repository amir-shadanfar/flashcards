<?php

namespace App\Console\Commands\Flashcard\Handlers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateHandler extends Command
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
    protected $signature = 'flashcard:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * @param \Illuminate\Console\Command $mainMenu
     */
    public function __construct(private Command $mainMenu)
    {
        parent::__construct();
    }

    /**
     * @return int
     */
    public function handle(): int
    {
        do {
            // getting input from user
            $question = $this->mainMenu->ask('Enter the question');
            $answer = $this->mainMenu->ask('Enter the answer');
            // validate user inputs
            $validator = Validator::make([
                'question' => $question,
                'answer' => $answer,
            ], [
                    'question' => 'required|min:5',
                    'answer' => 'required|min:1',
                ]
            );
            if ($validator->errors()) {
                $this->mainMenu->error($validator->errors()->first());
                continue;
            }
            // create flashcard
            $this->mainMenu->flashcardRepository->create($question, $answer);
            $this->mainMenu->info('New flashcard added.');

        } while ($this->mainMenu->confirm('Do you want to add more?', true));

        // back to the main menu
         return $this->mainMenu->handle();
    }
}
