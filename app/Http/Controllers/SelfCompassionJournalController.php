<?php

namespace App\Http\Controllers;

use App\Application\DTO\SelfCompassionJournalData;
use App\Application\UseCase\SelfCompassionJournal\CreateSelfCompassionJournalUseCase;
use App\Application\UseCase\SelfCompassionJournal\PresentSelfCompassionJournalUseCase;
use App\Application\UseCase\SelfCompassionJournal\SearchSelfCompassionJournalUseCase;
use App\Http\Requests\SelfCompassionJournal\CreateSelfCompassionJournalRequest;
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
     * セルフコンパッション日記を作成
     */
    public function store(
        CreateSelfCompassionJournalRequest $request,
        CreateSelfCompassionJournalUseCase $createSelfCompassionJournal,
        PresentSelfCompassionJournalUseCase $presentSelfCompassionJournal,
    ): JsonResponse {
        $data = new SelfCompassionJournalData(
            difficultExperience: (string) $request->string('difficult_experience'),
            effortMade: (string) $request->string('effort_made'),
            friendVoice: (string) $request->string('friend_voice'),
            wordToSelf: (string) $request->string('word_to_self'),
        );

        $journal = $createSelfCompassionJournal->handle($data);

        return response()->json($presentSelfCompassionJournal->handle($journal), 201);
    }
}
