<?php

namespace App\Enum;

enum FlashcardMenuEnum: string
{
    use EnumToArray;
    
    case Create = 'Create a flashcard';
    case List = 'List all flashcards';
    case Practice = 'Practice';
    case Stats = 'Stats';
    case Reset = 'Reset';
    case Exit = 'Exit';
    
}
