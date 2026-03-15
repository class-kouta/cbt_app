<?php

namespace App\Http\Controllers;

use App\Enums\MindfulnessDuration;
use App\Enums\MindfulnessSound;
use App\Http\Requests\Mindfulness\GetAudioUrlRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MindfulnessController extends Controller
{
    public function getAudioUrl(GetAudioUrlRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $sound = MindfulnessSound::from($validated['sound']);
        $duration = MindfulnessDuration::from((int) $validated['duration']);

        $path = "mindfulness/{$sound->value}_{$duration->value}m.wav";

        $baseUrl = config('services.mindfulness.audio_base_url');
        if ($baseUrl) {
            $url = rtrim($baseUrl, '/') . '/' . $path;
        } else {
            $url = Storage::disk('s3')->url($path);
        }

        return response()->json(['url' => $url]);
    }
}
