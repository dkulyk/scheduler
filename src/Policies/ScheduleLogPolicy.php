<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Policies;

use DKulyk\Scheduler\Entities\ScheduleLog;
use DtKt\Nova\Policies\ModelPolicy;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SchedulePolicy.
 */
class ScheduleLogPolicy extends ModelPolicy
{
    /**
     * @return Model
     */
    public static function model(): string
    {
        return ScheduleLog::class;
    }
}
