<?php

namespace App\Enum;

enum FlashcardMenuEnum: string
{
    use EnumToArray;
    
    case CREATE = 'Create a flashcard';
    case LIST = 'List all flashcards';
    case PRACTICE = 'Practice';
    case STATS = 'Stats';
    case RESET = 'Reset';
    case EXIT = 'Exit';
    
}
