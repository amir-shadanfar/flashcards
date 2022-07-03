<?php

namespace App\Repository;

use App\Models\Flashcard;
use App\Models\User;
use App\Enum\FlashcardStatusEnum;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface FlashcardRepositoryInterface
{
    public function create(string $question, string $answer): ?Model;

    public function list(array $columns = ['*']): Collection;

    public function answeredCorrectlyBefore(Authenticatable $user, int $flashcardId): bool;

    public function find(int $flashcardId): Model;

    public function updateAnswerStatus(Authenticatable $user, int $flashcardId, FlashcardStatusEnum $statusEnum): void;

    public function getAllWithCurrentUserAnswerStatus(Authenticatable $user): array;

    public function getCorrectlyAnsweredPercentage(Authenticatable $user): float;

    public function resetPracticeData(Authenticatable $user): void;

    public function getTotalCount(): int;

    public function getAnsweredPercentage(Authenticatable $user): float;
}
