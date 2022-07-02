<?php

namespace App\Console\Commands\Flashcard\Handlers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class ResetHandler extends Command
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
    protected $signature = 'flashcard:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset user practice data';

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
        if ($this->mainMenu->confirm('Are you sure to "reset" practice data?')) {
            // remove data
            $this->mainMenu->flashcardRepository->resetPracticeData(Auth::user());
            
            $this->mainMenu->info('Practice data has been reset!');
            sleep(1);
        }

        // back to the main menu
        $this->mainMenu->handle();

        return 0;
    }
}
