<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Nova\Resources;

use DKulyk\Scheduler\Nova\Actions\RunScheduleJob;
use Illuminate\Http\Resources\MergeValue;
use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Illuminate\Http\Request;
use DKulyk\Scheduler\Entities;
use Illuminate\Console\Scheduling\Schedule;
use RabbitCMS\Modules\Concerns\BelongsToModule;
use DKulyk\Scheduler\Nova\Actions\AddScheduleAction;
use DKulyk\Eloquent\Extensions\Nova\Filters\EnabledFilter;
use Laravel\Nova\Fields\{Boolean, HasMany, ID, Text, Textarea};

/**
 * Class ScheduleResource.
 * @property-read \DKulyk\Scheduler\Entities\Schedule $resource
 */
class ScheduleResource extends Resource
{
    use BelongsToModule;

    public static $model = Entities\Schedule::class;

    public static $globallySearchable = false;

    public static function uriKey(): string
    {
        return 'scheduler';
    }

    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }

    public static function group(): string
    {
        return self::module()->trans('scheduler.Settings');
    }

    public static function label(): string
    {
        return self::module()->trans('scheduler.Schedulers');
    }

    public function fields(Request $request): array
    {
        $module = static::module();

        return [
            ID::make(),
            Text::make($module->trans('scheduler.Caption'), 'caption')->rules('required'),
            Text::make($module->trans('scheduler.Event'), function () use ($module) {
                if (! is_null($this->resource->schedule)) {
                    return $module->trans('scheduler.Scheduled');
                }

                return $this->resource->event;
            })->exceptOnForms(),

            Textarea::make($module->trans('scheduler.Rules'), 'schedule')
                ->rules(['required', 'min:1'])
                ->hideFromIndex()
                ->alwaysShow()
                ->canSee(fn() => ! is_null($this->resource->schedule)),

            Text::make($module->trans('scheduler.Delay'), 'delay')
                ->rules(['regex:/^P(\d+Y)?(\d+M)?(\d+D)?(T(?=\d)(\d+H)?(\d+M)?(\d+S)?)?$/'])
                ->nullable(),

            Boolean::make($module->trans('scheduler.Enabled'), 'enabled'),

            Text::make($module->trans('scheduler.Job'), 'job')
                ->displayUsing(function () {
                    if (class_exists($this->resource->job ?? '')
                        && method_exists($this->resource->job, 'schedulerLabel')) {
                        return call_user_func([$this->resource->job, 'schedulerLabel']);
                    }

                    return $this->resource->job;
                })
                ->readonly(true),

            HasMany::make($module->trans('scheduler.Log'), 'logs', LogResource::class),

            $this->merge(function () use ($request) {
                if (class_exists($this->resource->job ?? '') && method_exists($this->resource->job, 'schedulerOptions')) {
                    return call_user_func([$this->resource->job, 'schedulerOptions'], $request, $this->resource->options);
                }

                return [];
            }),
        ];
    }

    public function filters(Request $request): array
    {
        return [
            new EnabledFilter(),
        ];
    }

    public function actions(Request $request): array
    {
        return [
            new AddScheduleAction(),
            new RunScheduleJob(),
        ];
    }

    public function availableActions(NovaRequest $request)
    {
        return parent::availableActions($request)
            ->when($this->resource->exists, fn(Collection $actions) => $actions
                ->filter->authorizedToRun($request, $this->resource));
    }
}
