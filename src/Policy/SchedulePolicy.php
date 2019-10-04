<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Policy;

use DtKt\Nova\Policies\ModelPolicy;
use DKulyk\Scheduler\Entities\Schedule;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SchedulePolicy.
 */
class SchedulePolicy extends ModelPolicy
{
    /**
     * @return Model
     */
    public static function model(): string
    {
        return Schedule::class;
    }
}
