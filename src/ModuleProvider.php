<?php

declare(strict_types=1);

namespace DKulyk\Scheduler;

use DKulyk\Scheduler\Entities\ScheduleLog;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

/**
 * Class ModuleProvider.
 */
final class ModuleProvider extends ServiceProvider
{
    public function register()
    {
        Relation::morphMap([
            'scheduler' => Schedule::class,
            'scheduler_log' => ScheduleLog::class,
        ]);

        $this->app->singleton(Scheduler::class, fn() => new Scheduler($this->app));

        $this->app->afterResolving(Schedule::class, function (Schedule $scheduler) {
            $registrar = new ScheduleRegistrar($scheduler);
            Entities\Schedule::query()->whereNotNull('schedule')
                ->each(fn(Entities\Schedule $schedule) => $registrar->register($schedule), 100);
        });
    }
}
