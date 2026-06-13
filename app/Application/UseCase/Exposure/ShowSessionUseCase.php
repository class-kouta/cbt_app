<?php

namespace App\Application\UseCase\Exposure;

use App\Application\Service\ExposureResponseFormatter;
use App\Infrastructure\Database\Models\ExposureSession;

class ShowSessionUseCase
{
    public function __construct(private readonly ExposureResponseFormatter $formatter)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(ExposureSession $session): array
    {
        return $this->formatter->sessionDetailFromModel($session);
    }
}
