<?php

namespace App\Console\Commands\Flashcard\Handlers;

use Illuminate\Console\Command;

class ListHandler extends Command
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
    protected $signature = 'flashcard:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all flashcards';

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
        do {
            $questions = $this->mainMenu->flashcardRepository->list(['id', 'question', 'answer'])->toArray();
            $this->mainMenu->table(['#', 'Questions', 'Answers'], $questions);
        } while (!$this->mainMenu->confirm('Back to main manu?', true));

        // back to the main menu
        $this->mainMenu->handle();

        return 0;
    }
}
