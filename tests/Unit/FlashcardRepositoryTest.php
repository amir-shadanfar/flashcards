<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repository\FlashcardRepository;
use App\Enum\FlashcardStatusEnum;
use App\Models\Flashcard;
use App\Models\User;
use Tests\TestCase;

/**
 * @coversDefaultClass \App\Repository\FlashcardRepository
 */
class FlashcardRepositoryTest extends TestCase
{
    /**
     * @var \App\Repository\FlashcardRepository
     */
    private FlashcardRepository $flashcardRepository;

    /**
     * @var \App\Models\User
     */
    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->initDatabase();
        $this->flashcardRepository = app(FlashcardRepository::class);
        // login a user
        $this->user = User::where('email', 'amir@gmail.com')->first();
        $this->actingAs($this->user);
    }

    public function tearDown(): void
    {
        $this->resetDatabase();
        parent::tearDown();
    }

    /**
     * @test
     * @covers ::create
     */
    public function test_create_a_new_flash_card()
    {
        $data = Flashcard::factory()->make();
        $result = $this->flashcardRepository->create($data['question'], $data['answer']);

        $this->assertSame($data['question'], $result->question);
        $this->assertSame($data['answer'], $result->answer);
    }

    /**
     * @test
     * @covers ::list
     */
    public function test_list_flash_cards()
    {
        $result = $this->flashcardRepository->list();
        // default seed 10 records
        $this->assertCount(10, $result);
    }

    /**
     * @test
     * @covers ::getTotalCount
     */
    public function test_get_total_count_flash_cards()
    {
        $result = $this->flashcardRepository->getTotalCount();
        // default seed 10 records
        $this->assertSame(10, $result);
    }

    /**
     * @test
     * @covers ::find
     */
    public function test_find_a_flash_card()
    {
        $flashcardObj = Flashcard::factory()->create();

        $result = $this->flashcardRepository->find($flashcardObj->id);

        $this->assertSame($flashcardObj->id, $result->id);
        $this->assertSame($flashcardObj->question, $result->question);
        $this->assertSame($flashcardObj->answer, $result->answer);
    }

    /**
     * @test
     * @covers ::updateAnswerStatus
     */
    public function test_update_answer_status_flash_card()
    {
        $flashcardObj = Flashcard::factory()->create();

        $this->flashcardRepository->updateAnswerStatus($this->user, $flashcardObj->id, FlashcardStatusEnum::CORRECT);
        $flashcardObj->load('users');
        $this->assertSame(FlashcardStatusEnum::CORRECT->value, $flashcardObj->users->where('id', $this->user->id)->first()->pivot->status);

        $this->flashcardRepository->updateAnswerStatus($this->user, $flashcardObj->id, FlashcardStatusEnum::INCORRECT);
        $flashcardObj->load('users');
        $this->assertSame(FlashcardStatusEnum::INCORRECT->value, $flashcardObj->users->where('id', $this->user->id)->first()->pivot->status);
    }

    /**
     * @test
     * @covers ::resetPracticeData
     */
    public function test_reset_practice_data()
    {
        $flashcardObj = Flashcard::factory()->create();
        $this->flashcardRepository->updateAnswerStatus($this->user, $flashcardObj->id, FlashcardStatusEnum::CORRECT);
        $flashcardObj->load('users');
        $this->assertCount(1, $flashcardObj->users->toArray());

        $this->flashcardRepository->resetPracticeData($this->user);
        $flashcardObj->load('users');

        $this->assertCount(0, $flashcardObj->users->toArray());
    }

    /**
     * @test
     * @covers ::answeredCorrectlyBefore
     */
    public function test_answered_correctly_before()
    {
        $flashcardObj = Flashcard::factory()->create();

        $this->flashcardRepository->updateAnswerStatus($this->user, $flashcardObj->id, FlashcardStatusEnum::CORRECT);
        $result = $this->flashcardRepository->answeredCorrectlyBefore($this->user, $flashcardObj->id);
        $this->assertTrue($result);

        $this->flashcardRepository->updateAnswerStatus($this->user, $flashcardObj->id, FlashcardStatusEnum::INCORRECT);
        $result = $this->flashcardRepository->answeredCorrectlyBefore($this->user, $flashcardObj->id);
        $this->assertFalse($result);
    }

    /**
     * @test
     * @covers ::getAllWithCurrentUserAnswerStatus
     */
    public function test_get_all_with_current_user_answer_status()
    {
        $flashcards = $this->flashcardRepository->getAllWithCurrentUserAnswerStatus($this->user);

        $this->assertCount(10, $flashcards);
        foreach ($flashcards as $flashcard) {
            $this->assertSame(FlashcardStatusEnum::NOT_ANSWERED->value, $flashcard['status']);
        }

        shuffle($flashcards);
        $randomFlashcard = reset($flashcards);
        $this->flashcardRepository->updateAnswerStatus($this->user, $randomFlashcard['id'], FlashcardStatusEnum::INCORRECT);

        $flashcards = $this->flashcardRepository->getAllWithCurrentUserAnswerStatus($this->user);
        foreach ($flashcards as $flashcard) {
            if (FlashcardStatusEnum::NOT_ANSWERED->value !== $flashcard['status'])
                $this->assertSame(FlashcardStatusEnum::INCORRECT->value, $flashcard['status']);
        }

    }

    /**
     * @test
     * @covers ::getAnsweredPercentage
     */
    public function test_get_answered_percentage()
    {
        $result = $this->flashcardRepository->getAnsweredPercentage($this->user);
        $this->assertSame(0.0, $result);

        foreach ($this->flashcardRepository->list() as $flashcard) {
            $this->flashcardRepository->updateAnswerStatus($this->user, $flashcard->id, $this->faker()->randomElement([
                FlashcardStatusEnum::INCORRECT,
                FlashcardStatusEnum::CORRECT,
            ]));
        }

        $result = $this->flashcardRepository->getAnsweredPercentage($this->user);
        $this->assertSame(100.0, $result);
    }

    /**
     * @test
     * @covers ::getCorrectlyAnsweredPercentage
     */
    public function test_get_correctly_answered_percentage()
    {
        $result = $this->flashcardRepository->getCorrectlyAnsweredPercentage($this->user);
        $this->assertSame(0.0, $result);

        foreach ($this->flashcardRepository->list() as $flashcard) {
            $this->flashcardRepository->updateAnswerStatus($this->user, $flashcard->id, FlashcardStatusEnum::CORRECT);
        }

        $result = $this->flashcardRepository->getAnsweredPercentage($this->user);
        $this->assertSame(100.0, $result);
    }

}
