<?php

namespace App\Infrastructure\Providers;

use App\Domain\Repository\CopingRepositoryInterface;
use App\Domain\Repository\CopingTagRepositoryInterface;
use App\Domain\Repository\ColumnRepositoryInterface;
use App\Domain\Repository\WritingDisclosureRepositoryInterface;
use App\Domain\Repository\ProblemSolvingRepositoryInterface;
use App\Domain\Repository\SimpleNotepadRepositoryInterface;
use App\Domain\Repository\StressorAndResponseRepositoryInterface;
use App\Domain\Repository\SupportNetworkRepositoryInterface;
use App\Infrastructure\Repository\EloquentCopingRepository;
use App\Infrastructure\Repository\EloquentCopingTagRepository;
use App\Infrastructure\Repository\EloquentColumnRepository;
use App\Infrastructure\Repository\EloquentWritingDisclosureRepository;
use App\Infrastructure\Repository\EloquentProblemSolvingRepository;
use App\Infrastructure\Repository\EloquentSimpleNotepadRepository;
use App\Infrastructure\Repository\EloquentStressorAndResponseRepository;
use App\Infrastructure\Repository\EloquentSupportNetworkRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CopingRepositoryInterface::class, EloquentCopingRepository::class);
        $this->app->bind(CopingTagRepositoryInterface::class, EloquentCopingTagRepository::class);
        $this->app->bind(ColumnRepositoryInterface::class, EloquentColumnRepository::class);
        $this->app->bind(WritingDisclosureRepositoryInterface::class, EloquentWritingDisclosureRepository::class);
        $this->app->bind(ProblemSolvingRepositoryInterface::class, EloquentProblemSolvingRepository::class);
        $this->app->bind(SimpleNotepadRepositoryInterface::class, EloquentSimpleNotepadRepository::class);
        $this->app->bind(StressorAndResponseRepositoryInterface::class, EloquentStressorAndResponseRepository::class);
        $this->app->bind(SupportNetworkRepositoryInterface::class, EloquentSupportNetworkRepository::class);
    }
}
