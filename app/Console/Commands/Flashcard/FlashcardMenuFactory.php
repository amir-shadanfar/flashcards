<?php

namespace App\Console\Commands\Flashcard;

use Illuminate\Console\Command;
use App\Enum\FlashcardMenuEnum;

class FlashcardMenuFactory
{
    /**
     * @param string $choice
     * @param \Illuminate\Console\Command $mainMenu
     *
     * @return \Illuminate\Console\Command
     */
    public static function create(string $choice, Command $mainMenu): Command
    {
        $choosen = null;
        foreach (FlashcardMenuEnum::cases() as $item) {
            if ($choice == $item->value) {
                $choosen = $item->name;
            }
        }

        $class = sprintf('\\%s\Handlers\\%sHandler', __NAMESPACE__, $choosen);
        return new $class(mainMenu: $mainMenu);
    }
}
