<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Scheduler.
 * @method static \DKulyk\Scheduler\Scheduler registerJob(string $job)
 * @method static string[] getJobs()
 */
final class Scheduler extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \DKulyk\Scheduler\Scheduler::class;
    }
}
