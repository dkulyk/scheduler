<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Nova\Actions;

use AwesomeNova\Actions\ToolAction;
use DKulyk\Scheduler\Entities\Schedule;
use DKulyk\Scheduler\Facades\Scheduler;
use Laravel\Nova\Fields\{ActionFields, Select, Text, Textarea};
use RabbitCMS\Modules\Concerns\BelongsToModule;

/**
 * Class AddScheduleAction.
 */
class AddScheduleAction extends ToolAction
{
    use BelongsToModule;

    /**
     * @return string
     */
    public function name()
    {
        return self::module()->trans('scheduler.Adding schedule');
    }

    /**
     * @return string
     */
    public function uriKey()
    {
        return 'add-schedule';
    }

    /**
     * @return string
     */
    public function label()
    {
        return self::module()->trans('scheduler.Add schedule');
    }

    /**
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     */
    public function handle(ActionFields $fields)
    {
        Schedule::query()->create($fields->getAttributes());
    }

    /**
     * @return array
     */
    public function fields()
    {
        $module = self::module();

        return [
            Text::make($module->trans('scheduler.Caption'), 'caption')->rules('required'),

            Textarea::make($module->trans('scheduler.Rules'), 'schedule')
                ->rules(['required', 'min:1'])->withMeta([
                    'value' => 'daily',
                ]),

            Text::make($module->trans('scheduler.Delay'), 'delay')
                ->rules(['regex:/^P(\d+Y)?(\d+M)?(\d+D)?(T(?=\d)(\d+H)?(\d+M)?(\d+S)?)?$/'])
                ->nullable(),

            Select::make($module->trans('scheduler.Job'), 'job')
                ->options(collect($jobs = Scheduler::getJobs())->mapWithKeys(function (string $job) {
                    if (class_exists($job)
                        && method_exists($job, 'schedulerLabel')) {
                        return [$job => call_user_func([$job, 'schedulerLabel'])];
                    }
                    return [$job => $job];
                })->all())
                ->rules('required')
                ->displayUsingLabels(),
        ];
    }
}
