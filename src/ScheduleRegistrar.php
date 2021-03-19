<?php

declare(strict_types=1);

namespace DKulyk\Scheduler;

use Carbon\Carbon;
use DKulyk\Scheduler\Entities\ScheduleLog;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Dispatcher;
use DKulyk\Scheduler\Jobs\ScheduleJob;
use Illuminate\Console\Scheduling\{CallbackEvent, Schedule};
use Illuminate\Support\Facades\Bus;

/**
 * Class ScheduleRegistrar.
 */
class ScheduleRegistrar
{
    private static array $allowed = [
        'cron',
        'everyMinute',
        'everyTwoMinutes',
        'everyThreeMinutes',
        'everyFourMinutes',
        'everyFiveMinutes',
        'everyTenMinutes',
        'everyFifteenMinutes',
        'everyThirtyMinutes',
        'hourly',
        'hourlyAt',
        'everyTwoHours',
        'everyThreeHours',
        'everyFourHours',
        'everySixHours',
        'daily',
        'dailyAt',
        'twiceDaily',
        'weekly',
        'weeklyOn',
        'monthly',
        'monthlyOn',
        'monthlyOnLastDay',
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

    public function __construct(private Schedule $scheduler)
    {
    }

    public function register(Entities\Schedule $schedule): CallbackEvent
    {
        $event = $this->scheduler->call(function (Scheduler $scheduler) use ($schedule) {
            $scheduler->run($schedule);
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
