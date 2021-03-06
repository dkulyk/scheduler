<?php

declare(strict_types=1);

namespace DKulyk\Scheduler;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use DKulyk\Scheduler\Entities;

final class ModuleProvider extends ServiceProvider
{
    public function register()
    {
        Relation::morphMap([
            'scheduler' => Entities\Schedule::class,
            'scheduler_log' => Entities\ScheduleLog::class,
        ]);

        $this->app->singleton(Scheduler::class, fn() => new Scheduler($this->app));

        $this->app->afterResolving(Schedule::class, function (Schedule $scheduler) {
            $registrar = new ScheduleRegistrar($scheduler);
            Entities\Schedule::query()->whereNotNull('schedule')
                ->each(fn(Entities\Schedule $schedule) => $registrar->register($schedule), 100);
        });
    }
}
