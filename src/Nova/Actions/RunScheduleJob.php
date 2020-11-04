<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Nova\Actions;


use Closure;
use DKulyk\Scheduler\Entities\Schedule;
use DKulyk\Scheduler\Jobs\ScheduleJob;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class RunScheduleJob extends Action
{
    public $showOnIndex = false;
    public $showOnTableRow = true;
    public $runCallback = true;

    public function handle(ActionFields $fields, Collection $collection): void
    {
        $collection->each(fn(Schedule $schedule) => dispatch(new ScheduleJob($schedule)));
    }

    public function authorizedToRun(Request $request, $model): bool
    {
        return class_exists($model->job ?? '') && method_exists($model->job, 'schedulerCanRunImmediate') && call_user_func([$model->job, 'schedulerCanRunImmediate']);
    }
}
