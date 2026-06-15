<?php

namespace App\Http\Controllers;

use App\Application\DTO\SelfCompassionJournalData;
use App\Application\UseCase\SelfCompassionJournal\CreateSelfCompassionJournalUseCase;
use App\Application\UseCase\SelfCompassionJournal\DeleteSelfCompassionJournalUseCase;
use App\Application\UseCase\SelfCompassionJournal\FindSelfCompassionJournalUseCase;
use App\Application\UseCase\SelfCompassionJournal\PresentSelfCompassionJournalUseCase;
use App\Application\UseCase\SelfCompassionJournal\SearchSelfCompassionJournalUseCase;
use App\Application\UseCase\SelfCompassionJournal\UpdateSelfCompassionJournalUseCase;
use App\Http\Requests\SelfCompassionJournal\CreateSelfCompassionJournalRequest;
use App\Http\Requests\SelfCompassionJournal\UpdateSelfCompassionJournalRequest;
use DomainException;
use Illuminate\Http\JsonResponse;

class SelfCompassionJournalController extends Controller
{
    /**
     * セルフコンパッション日記一覧を取得（作成日時降順）
     */
    public function index(SearchSelfCompassionJournalUseCase $searchSelfCompassionJournal): JsonResponse
    {
        return response()->json($searchSelfCompassionJournal->handle());
    }

    /**
     * セルフコンパッション日記詳細を取得
     */
    public function show(
        int $selfCompassionJournal,
        FindSelfCompassionJournalUseCase $findSelfCompassionJournal,
        PresentSelfCompassionJournalUseCase $presentSelfCompassionJournal,
    ): JsonResponse {
        try {
            $journal = $findSelfCompassionJournal->handle($selfCompassionJournal);
        } catch (DomainException) {
            abort(404);
        }

        return response()->json($presentSelfCompassionJournal->handle($journal));
    }

    /**
     * セルフコンパッション日記を作成
     */
    public function store(
        CreateSelfCompassionJournalRequest $request,
        CreateSelfCompassionJournalUseCase $createSelfCompassionJournal,
        PresentSelfCompassionJournalUseCase $presentSelfCompassionJournal,
    ): JsonResponse {
        $data = $this->toData($request);

        $journal = $createSelfCompassionJournal->handle($data);

        return response()->json($presentSelfCompassionJournal->handle($journal), 201);
    }

    /**
     * セルフコンパッション日記を更新
     */
    public function update(
        UpdateSelfCompassionJournalRequest $request,
        int $selfCompassionJournal,
        UpdateSelfCompassionJournalUseCase $updateSelfCompassionJournal,
        PresentSelfCompassionJournalUseCase $presentSelfCompassionJournal,
    ): JsonResponse {
        $data = $this->toData($request);

        try {
            $journal = $updateSelfCompassionJournal->handle($selfCompassionJournal, $data);
        } catch (DomainException) {
            abort(404);
        }

        return response()->json($presentSelfCompassionJournal->handle($journal));
    }

    /**
     * セルフコンパッション日記を削除
     */
    public function destroy(
        int $selfCompassionJournal,
        DeleteSelfCompassionJournalUseCase $deleteSelfCompassionJournal,
    ): JsonResponse {
        try {
            $deleteSelfCompassionJournal->handle($selfCompassionJournal);
        } catch (DomainException) {
            abort(404);
        }

        return response()->json(null, 204);
    }

    private function toData(CreateSelfCompassionJournalRequest $request): SelfCompassionJournalData
    {
        $validated = $request->validated();

        return new SelfCompassionJournalData(
            difficultExperience: $validated['difficult_experience'],
            effortMade: $validated['effort_made'],
            friendVoice: $validated['friend_voice'],
            wordToSelf: $validated['word_to_self'],
        );
    }
}
