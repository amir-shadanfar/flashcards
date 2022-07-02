<?php

namespace App\Console\Commands\Flashcard;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\Enum\FlashcardMenuEnum;
use App\Repository\FlashcardRepositoryInterface;

class FlashcardMainMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flashcard:interactive {--email=amir@gmail.com} {--password=123}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start flashcards application';

    /**
     * inject flashcard repo to use inside all submenu
     * 
     * @param \App\Repository\FlashcardRepositoryInterface $flashcardRepository
     */
    public function __construct(public FlashcardRepositoryInterface $flashcardRepository)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // check credentials
        $credentials = [
            'email' => $this->option('email'),
            'password' => $this->option('password'),
        ];
        if (!Auth::attempt($credentials)) {
            $this->error('Authentication is failed!');
            return 0;
        }

        $this->info('Welcome, ' . Auth::user()->name);
        $choice = $this->choice('Please choose your action?', FlashcardMenuEnum::values(), 1, 5);

        // show menu until user want to exit
        while ($choice !== FlashcardMenuEnum::Exit->value) {
            // call submenu according the user input
            $action = FlashcardMenuFactory::create($choice, $this);
            $action->handle();
        }

        return 0;
    }
}
