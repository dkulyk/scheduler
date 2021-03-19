<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Nova\Actions;

use AwesomeNova\Actions\ToolAction;
use DKulyk\Scheduler\Entities\Schedule;
use DKulyk\Scheduler\Facades\Scheduler;
use RabbitCMS\Modules\Concerns\BelongsToModule;
use Laravel\Nova\Fields\{ActionFields, Select, Text, Textarea};

class AddScheduleAction extends ToolAction
{
    use BelongsToModule;

    public function __construct()
    {
        $this->onlyOnIndex()->standalone();
    }

    public function name(): string
    {
        return self::module()->trans('scheduler.Adding schedule');
    }

    public function uriKey(): string
    {
        return 'add-schedule';
    }

    public function label(): string
    {
        return self::module()->trans('scheduler.Add schedule');
    }

    public function handle(ActionFields $fields): void
    {
        Schedule::query()->create($fields->getAttributes());
    }

    public function fields(): array
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
                    return [
                        $job => class_exists($job) && method_exists($job, 'schedulerLabel')
                            ? call_user_func([$job, 'schedulerLabel'])
                            : $job,
                    ];
                })->all())
                ->rules('required')
                ->displayUsingLabels(),
        ];
    }
}
