<?php

namespace App\Application\UseCase\Exposure;

use App\Application\Service\ExposureResponseFormatter;
use App\Domain\Repository\ExposureRepositoryInterface;
use App\Infrastructure\Database\Models\ExposureSession;
use Illuminate\Support\Facades\Auth;

class ShowSessionUseCase
{
    public function __construct(
        private readonly ExposureRepositoryInterface $repository,
        private readonly ExposureResponseFormatter $formatter
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(ExposureSession $session): array
    {
        $session->loadMissing(['exposure', 'hierarchyItem']);

        $entity = $this->repository->findSessionByIdForMember($session->id, (int) Auth::id());

        if ($entity === null) {
            abort(404);
        }

        return $this->formatter->sessionSearchRowFromEntity(
            $entity,
            $session->exposure->avoidance_target ?? '',
            $session->hierarchyItem->content ?? ''
        );
    }
}
