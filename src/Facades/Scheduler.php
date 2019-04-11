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
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \DKulyk\Scheduler\Scheduler::class;
    }
}
