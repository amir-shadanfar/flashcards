<?php

namespace App\Enum;

enum FlashcardEnum: string
{
    use EnumToArray;
    
    case CORRECT = 'Correct';
    case INCORRECT = 'Incorrect';
    case NOT_ANSWERED = 'Not Answered';
}
