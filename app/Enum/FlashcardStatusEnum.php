<?php

namespace App\Enum;

enum FlashcardStatusEnum: string
{
    use EnumToArray;
    
    case CORRECT = 'Correct';
    case INCORRECT = 'Incorrect';
    case NOT_ANSWERED = 'Not Answered';
}
