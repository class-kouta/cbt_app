<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnxietyDiaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getId(),
            'situation' => $this->getSituation(),
            'anxiety_thought' => $this->getAnxietyThought(),
            'actual_outcome' => $this->getActualOutcome(),
            'stressor_and_response_id' => $this->getStressorAndResponseId(),
            'created_at' => $this->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $this->getUpdatedAt()->format(DATE_ATOM),
        ];
    }
}
