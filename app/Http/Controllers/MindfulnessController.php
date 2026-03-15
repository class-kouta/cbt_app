<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mindfulness\GetAudioUrlRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MindfulnessController extends Controller
{
    private const SOUND_FILE_NAMES = [
        'forest' => '森と木陰と風、鳥の鳴き声',
        'stream' => '夕暮れの小川、鈴虫と風',
        'jungle' => '雷雨のジャングルと動物達',
    ];

    public function getAudioUrl(GetAudioUrlRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $sound = $validated['sound'];
        $duration = $validated['duration'];

        $fileName = self::SOUND_FILE_NAMES[$sound];
        $path = "mindfulness/{$fileName}_{$duration}m.wav";

        $baseUrl = config('services.mindfulness.audio_base_url');
        if ($baseUrl) {
            $url = rtrim($baseUrl, '/') . '/' . $path;
        } else {
            $url = Storage::disk('s3')->url($path);
        }

        return response()->json(['url' => $url]);
    }
}
