<?php

namespace App\Infrastructure\Providers;

use App\Domain\Repository\TodoRepositoryInterface;
use App\Infrastructure\Repository\EloquentTodoRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TodoRepositoryInterface::class, EloquentTodoRepository::class);
    }
}

