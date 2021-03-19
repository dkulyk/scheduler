<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Nova\Actions;

use Closure;
use DKulyk\Scheduler\Entities\Schedule;
use DKulyk\Scheduler\Facades\Scheduler;
use DKulyk\Scheduler\Jobs\ScheduleJob;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use RabbitCMS\Modules\Concerns\BelongsToModule;

class RunScheduleJob extends Action
{
    use BelongsToModule;

    public $showOnIndex = false;
    public $showOnTableRow = true;
    public $runCallback = true;

    public function name(): string
    {
        return self::module()->trans('scheduler.Run now');
    }

    public function uriKey(): string
    {
        return 'run';
    }

    public function handle(ActionFields $fields, Collection $collection): void
    {
        $collection->each(fn(Schedule $schedule) => Scheduler::run($schedule));
    }

    public function authorizedToRun(Request $request, $model): bool
    {
        return class_exists($model->job ?? '') && method_exists($model->job, 'schedulerCanRunImmediate') && call_user_func([$model->job, 'schedulerCanRunImmediate']);
    }
}
