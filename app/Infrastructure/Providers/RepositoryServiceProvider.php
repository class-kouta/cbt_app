<?php

namespace App\Infrastructure\Providers;

use App\Domain\Repository\TodoRepositoryInterface;
use App\Domain\Repository\CopingRepositoryInterface;
use App\Domain\Repository\CopingTagRepositoryInterface;
use App\Domain\Repository\ColumnRepositoryInterface;
use App\Domain\Repository\QuickTaskRepositoryInterface;
use App\Infrastructure\Repository\EloquentTodoRepository;
use App\Infrastructure\Repository\EloquentCopingRepository;
use App\Infrastructure\Repository\EloquentCopingTagRepository;
use App\Infrastructure\Repository\EloquentColumnRepository;
use App\Infrastructure\Repository\EloquentQuickTaskRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TodoRepositoryInterface::class, EloquentTodoRepository::class);
        $this->app->bind(CopingRepositoryInterface::class, EloquentCopingRepository::class);
        $this->app->bind(CopingTagRepositoryInterface::class, EloquentCopingTagRepository::class);
        $this->app->bind(ColumnRepositoryInterface::class, EloquentColumnRepository::class);
        $this->app->bind(QuickTaskRepositoryInterface::class, EloquentQuickTaskRepository::class);
    }
}

