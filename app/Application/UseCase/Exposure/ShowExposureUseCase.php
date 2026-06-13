<?php

namespace App\Application\UseCase\Exposure;

use App\Application\Service\ExposureResponseFormatter;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class ShowExposureUseCase
{
    public function __construct(
        private readonly ExposureRepositoryInterface $repository,
        private readonly ExposureResponseFormatter $formatter
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function handle(int $id): array
    {
        $exposure = $this->repository->findByIdForMember($id, (int) Auth::id());

        if ($exposure === null) {
            throw (new ModelNotFoundException)->setModel('Exposure', $id);
        }

        return $this->formatter->exposureFromEntity($exposure);
    }
}
