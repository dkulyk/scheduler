<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Facades;

use DKulyk\Scheduler\Entities\Schedule;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Facade;

/**
 * Class Scheduler.
 * @method static \DKulyk\Scheduler\Scheduler registerJob(string $job)
 * @method static string[] getJobs()
 * @method static Batch run(Schedule $schedule)
 */
final class Scheduler extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \DKulyk\Scheduler\Scheduler::class;
    }
}
