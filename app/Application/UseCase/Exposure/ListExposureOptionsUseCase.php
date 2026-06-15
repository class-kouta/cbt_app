<?php

namespace App\Application\UseCase\Exposure;

use App\Application\Service\ExposureResponseFormatter;
use App\Domain\Repository\ExposureRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ListExposureOptionsUseCase
{
    public function __construct(
        private readonly ExposureRepositoryInterface $repository,
        private readonly ExposureResponseFormatter $formatter
    ) {
    }

    /**
     * @return array<int, array{id: int, avoidance_target: string}>
     */
    public function handle(): array
    {
        $options = $this->repository->listOptionsForMember((int) Auth::id());

        return array_map(
            fn (array $option) => $this->formatter->optionFromArray($option),
            $options
        );
    }
}
