<?php

declare(strict_types=1);

namespace DKulyk\Scheduler;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class ModuleProvider.
 */
final class ModuleProvider extends ServiceProvider
{
    public $defer = true;

    public function register()
    {
        $this->app->singleton('dkulyk.scheduler', function () {
            return new Scheduler();
        });

        $this->app->alias('dkulyk.scheduler', Scheduler::class);

        $this->app->afterResolving(Schedule::class, function (Schedule $scheduler) {
            $registrar = new ScheduleRegistrar($scheduler);
            Entities\Schedule::query()->whereNotNull('schedule')
                ->each(function (Entities\Schedule $schedule) use ($registrar) {
                    $registrar->register($schedule);
                }, 100);
        });
    }

    public function boot()
    {
        Relation::morphMap([
            'scheduler' => Scheduler::class,
            'scheduler_log' => Entities\ScheduleLog::class,
        ]);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            'dkulyk.scheduler',
            Scheduler::class,
        ];
    }
}
