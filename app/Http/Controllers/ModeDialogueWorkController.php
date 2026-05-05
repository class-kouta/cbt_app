<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModeDialogueWork\CreateModeDialogueWorkRequest;
use App\Http\Requests\ModeDialogueWork\UpdateModeDialogueWorkRequest;
use App\Infrastructure\Database\Models\DialogueWork;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ModeDialogueWorkController extends Controller
{
    private const MODE_TYPE = 'mode';

    public function index(): JsonResponse
    {
        $items = DialogueWork::where('type', self::MODE_TYPE)
            ->where('member_id', (int) Auth::id())
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (DialogueWork $model) => $this->toResponse($model))
            ->toArray();

        return response()->json($items);
    }

    public function show(DialogueWork $modeDialogueWork): JsonResponse
    {
        $this->assertModeType($modeDialogueWork);

        return response()->json($this->toResponse($modeDialogueWork));
    }

    public function store(CreateModeDialogueWorkRequest $request): JsonResponse
    {
        $model = DialogueWork::create([
            'type' => self::MODE_TYPE,
            'content' => (string) $request->input('content'),
            'mode_category' => (string) $request->input('mode_category'),
            'mode_name' => (string) $request->input('mode_name'),
            'member_id' => (int) Auth::id(),
        ]);

        return response()->json($this->toResponse($model), 201);
    }

    public function update(UpdateModeDialogueWorkRequest $request, DialogueWork $modeDialogueWork): JsonResponse
    {
        $this->assertModeType($modeDialogueWork);

        $modeDialogueWork->update([
            'content' => (string) $request->input('content'),
        ]);

        return response()->json($this->toResponse($modeDialogueWork));
    }

    public function destroy(DialogueWork $modeDialogueWork): JsonResponse
    {
        $this->assertModeType($modeDialogueWork);

        $modeDialogueWork->delete();

        return response()->json(null, 204);
    }

    private function assertModeType(DialogueWork $modeDialogueWork): void
    {
        if ($modeDialogueWork->type !== self::MODE_TYPE) {
            abort(404);
        }
    }

    /**
     * @return array<string, int|string>
     */
    private function toResponse(DialogueWork $model): array
    {
        return [
            'id' => $model->id,
            'content' => $model->content,
            'mode_category' => $model->mode_category,
            'mode_name' => $model->mode_name,
            'created_at' => $model->created_at->format(DATE_ATOM),
            'updated_at' => $model->updated_at->format(DATE_ATOM),
        ];
    }
}
