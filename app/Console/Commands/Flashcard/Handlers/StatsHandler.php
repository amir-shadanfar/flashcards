<?php

namespace App\Console\Commands\Flashcard\Handlers;

use App\Models\Flashcard;
use App\Enum\FlashcardStatusEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class StatsHandler extends Command
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
    protected $signature = 'flashcard:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the statistics of user progress';

    /**
     * @param \Illuminate\Console\Command $mainMenu
     */
    public function __construct(private Command $mainMenu)
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        do {
            $this->mainMenu->table(['Metrics', 'Statistics'], [
                    [
                        'key' => 'Total Number Of Questions',
                        'value' => $this->mainMenu->flashcardRepository->getTotalCount()
                    ], [
                        'key' => 'Answered Questions',
                        'value' => sprintf('%s %%', $this->mainMenu->flashcardRepository->getAnsweredPercentage(Auth::user()))
                    ], [
                        'key' => 'Correctly Answered Questions',
                        'value' => sprintf('%s %%', $this->mainMenu->flashcardRepository->getCorrectlyAnsweredPercentage(Auth::user()))
                    ]
                ]
            );
        } while (!$this->mainMenu->confirm('Back to main manu?', true));

        // back to the main menu
        return $this->mainMenu->handle();
    }
}
