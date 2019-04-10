<?php

declare(strict_types=1);

namespace DKulyk\Scheduler;

use Illuminate\Bus\Dispatcher;
use DKulyk\Scheduler\Entities;
use DKulyk\Scheduler\Jobs\ScheduleJob;
use Illuminate\Console\Scheduling\Schedule;

/**
 * Class ScheduleRegistrar.
 */
class ScheduleRegistrar
{
    private static $allowed = [
        'cron',
        'everyMinute',
        'everyFiveMinutes',
        'everyTenMinutes',
        'everyFifteenMinutes',
        'everyThirtyMinutes',
        'hourly',
        'hourlyAt',
        'daily',
        'dailyAt',
        'weekly',
        'weeklyOn',
        'monthly',
        'monthlyOn',
        'quarterly',
        'yearly',
        'at',
        'days',
        'weekdays',
        'weekends',
        'sundays',
        'mondays',
        'tuesdays',
        'wednesdays',
        'thursdays',
        'fridays',
        'saturdays',
        'between',
    ];
    /**
     * @var \Illuminate\Console\Scheduling\Schedule
     */
    private $scheduler;

    /**
     * ScheduleRegistrar constructor.
     * @param  \Illuminate\Console\Scheduling\Schedule  $scheduler
     */
    public function __construct(Schedule $scheduler)
    {
        $this->scheduler = $scheduler;
    }

    /**
     * @param  \DKulyk\Scheduler\Entities\Schedule  $schedule
     * @return \Illuminate\Console\Scheduling\CallbackEvent
     */
    public function register(Entities\Schedule $schedule)
    {
        $event = $this->scheduler->call(function (Dispatcher $dispatcher) use ($schedule) {
            $dispatcher->dispatch(new ScheduleJob($schedule));
        });

        foreach (preg_split('/\\r?\\n/', $schedule->schedule) as $line) {
            $line = explode(':', $line, 2);
            $line[1] = empty($line[1]) ? [] : explode(',', $line[1]);

            if (in_array($line[0], static::$allowed)) {
                $event = call_user_func_array([$event, $line[0]], $line[1]);
            }
        }

        return $event;
    }
}
