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
use App\Domain\Repository\EarlyMaladaptiveSchemaRepositoryInterface;
use App\Domain\Repository\TagRepositoryInterface;
use App\Domain\Repository\ChronologyRepositoryInterface;
use App\Domain\Repository\ModeMapRepositoryInterface;
use App\Domain\Repository\SchemaModeMonitoringRepositoryInterface;
use App\Domain\Repository\DialogueWorkRepositoryInterface;
use App\Domain\Repository\HealthyAdultModeImageRepositoryInterface;
use App\Domain\Repository\MemberRepositoryInterface;

use App\Infrastructure\Repository\EloquentCopingRepository;
use App\Infrastructure\Repository\EloquentCopingTagRepository;
use App\Infrastructure\Repository\EloquentColumnRepository;
use App\Infrastructure\Repository\EloquentWritingDisclosureRepository;
use App\Infrastructure\Repository\EloquentProblemSolvingRepository;
use App\Infrastructure\Repository\EloquentSimpleNotepadRepository;
use App\Infrastructure\Repository\EloquentStressorAndResponseRepository;
use App\Infrastructure\Repository\EloquentSupportNetworkRepository;
use App\Infrastructure\Repository\EloquentEarlyMaladaptiveSchemaRepository;
use App\Infrastructure\Repository\EloquentTagRepository;
use App\Infrastructure\Repository\EloquentChronologyRepository;
use App\Infrastructure\Repository\EloquentModeMapRepository;
use App\Infrastructure\Repository\EloquentSchemaModeMonitoringRepository;
use App\Infrastructure\Repository\EloquentDialogueWorkRepository;
use App\Infrastructure\Repository\EloquentHealthyAdultModeImageRepository;
use App\Infrastructure\Repository\EloquentMemberRepository;

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
        $this->app->bind(EarlyMaladaptiveSchemaRepositoryInterface::class, EloquentEarlyMaladaptiveSchemaRepository::class);
        $this->app->bind(TagRepositoryInterface::class, EloquentTagRepository::class);
        $this->app->bind(ChronologyRepositoryInterface::class, EloquentChronologyRepository::class);
        $this->app->bind(ModeMapRepositoryInterface::class, EloquentModeMapRepository::class);
        $this->app->bind(SchemaModeMonitoringRepositoryInterface::class, EloquentSchemaModeMonitoringRepository::class);
        $this->app->bind(DialogueWorkRepositoryInterface::class, EloquentDialogueWorkRepository::class);
        $this->app->bind(HealthyAdultModeImageRepositoryInterface::class, EloquentHealthyAdultModeImageRepository::class);
        $this->app->bind(MemberRepositoryInterface::class, EloquentMemberRepository::class);
    }
}
