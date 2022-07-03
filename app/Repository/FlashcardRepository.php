<?php

namespace App\Repository;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Flashcard;
use App\Enum\FlashcardStatusEnum;

class FlashcardRepository implements FlashcardRepositoryInterface
{
    /**
     * @param \App\Models\Flashcard $model
     */
    public function __construct(protected Flashcard $model)
    {
    }

    /**
     * @param string $question
     * @param string $answer
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function create(string $question, string $answer): ?Model
    {
        return $this->model->create([
            'question' => $question,
            'answer' => $answer,
        ]);
    }

    /**
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function list(array $columns = ['*']): Collection
    {
        return $this->model::all($columns);
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param int $flashcardId
     *
     * @return bool
     */
    public function answeredCorrectlyBefore(Authenticatable $user, int $flashcardId): bool
    {
        return (bool)$user->flashcards()->wherePivot('status', FlashcardStatusEnum::CORRECT)->wherePivot('flashcard_id', $flashcardId)->first();
    }

    /**
     * @param int $flashcardId
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find(int $flashcardId): Model
    {
        return $this->model::find($flashcardId);
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param int $flashcardId
     * @param \App\Enum\FlashcardStatusEnum $statusEnum
     *
     * @return void
     */
    public function updateAnswerStatus(Authenticatable $user, int $flashcardId, FlashcardStatusEnum $statusEnum): void
    {
        $user->flashcards()->syncWithoutDetaching([$flashcardId => ['status' => $statusEnum->value]]);
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return array
     */
    public function getAllWithCurrentUserAnswerStatus(Authenticatable $user): array
    {
        $AllCardsOfhUsers = Flashcard::with(['users' => function ($q) use ($user) {
            $q->where('id', $user->id)->select('flashcard_user.status');
        }])->get(['id', 'question']);

        $AllCardsWithCurrentUserAnswerStatus = [];
        foreach ($AllCardsOfhUsers as $userFlashcard) {
            $AllCardsWithCurrentUserAnswerStatus[] = [
                'id' => $userFlashcard->id,
                'question' => $userFlashcard->question,
                'status' => $userFlashcard->users->first()->status ?? FlashcardStatusEnum::NOT_ANSWERED->value,
            ];
        }

        return $AllCardsWithCurrentUserAnswerStatus;
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return float
     */
    public function getCorrectlyAnsweredPercentage(Authenticatable $user): float
    {
        if ($corrects = $user->flashcards()->wherePivot('status', FlashcardStatusEnum::CORRECT->value)->count()) {
            return round($corrects / $this->getTotalCount() * 100, 2);
        }
        return 0.0;
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return void
     */
    public function resetPracticeData(Authenticatable $user): void
    {
        $user->flashcards()->detach();
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->model->count();
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return float
     */
    public function getAnsweredPercentage(Authenticatable $user): float
    {
        if ($answered = $user->flashcards()->count()) {
            return round($answered / $this->getTotalCount() * 100, 2);
        }

        return 0.0;
    }

}
