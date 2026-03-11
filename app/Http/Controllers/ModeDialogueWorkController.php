<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModeDialogueWork\CreateModeDialogueWorkRequest;
use App\Http\Requests\ModeDialogueWork\UpdateModeDialogueWorkRequest;
use App\Infrastructure\Database\Models\DialogueWork;
use Illuminate\Http\JsonResponse;

class ModeDialogueWorkController extends Controller
{
    public function index(): JsonResponse
    {
        $items = DialogueWork::where('type', 'mode')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($model) => [
                'id' => $model->id,
                'content' => $model->content,
                'mode_category' => $model->mode_category,
                'mode_name' => $model->mode_name,
                'created_at' => $model->created_at->format(DATE_ATOM),
                'updated_at' => $model->updated_at->format(DATE_ATOM),
            ])
            ->toArray();

        return response()->json($items);
    }

    public function show(DialogueWork $modeDialogueWork): JsonResponse
    {
        if ($modeDialogueWork->type !== 'mode') {
            abort(404);
        }

        return response()->json([
            'id' => $modeDialogueWork->id,
            'content' => $modeDialogueWork->content,
            'mode_category' => $modeDialogueWork->mode_category,
            'mode_name' => $modeDialogueWork->mode_name,
            'created_at' => $modeDialogueWork->created_at->format(DATE_ATOM),
            'updated_at' => $modeDialogueWork->updated_at->format(DATE_ATOM),
        ]);
    }

    public function store(CreateModeDialogueWorkRequest $request): JsonResponse
    {
        $model = DialogueWork::create([
            'type' => 'mode',
            'content' => $request->string('content'),
            'mode_category' => $request->string('mode_category'),
            'mode_name' => $request->string('mode_name'),
        ]);

        return response()->json([
            'id' => $model->id,
            'content' => $model->content,
            'mode_category' => $model->mode_category,
            'mode_name' => $model->mode_name,
            'created_at' => $model->created_at->format(DATE_ATOM),
            'updated_at' => $model->updated_at->format(DATE_ATOM),
        ], 201);
    }

    public function update(UpdateModeDialogueWorkRequest $request, DialogueWork $modeDialogueWork): JsonResponse
    {
        $modeDialogueWork->update([
            'content' => $request->string('content'),
        ]);

        return response()->json([
            'id' => $modeDialogueWork->id,
            'content' => $modeDialogueWork->content,
            'mode_category' => $modeDialogueWork->mode_category,
            'mode_name' => $modeDialogueWork->mode_name,
            'created_at' => $modeDialogueWork->created_at->format(DATE_ATOM),
            'updated_at' => $modeDialogueWork->updated_at->format(DATE_ATOM),
        ]);
    }

    public function destroy(DialogueWork $modeDialogueWork): JsonResponse
    {
        if ($modeDialogueWork->type !== 'mode') {
            abort(404);
        }

        $modeDialogueWork->delete();

        return response()->json(null, 204);
    }
}
