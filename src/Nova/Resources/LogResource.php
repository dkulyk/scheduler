<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Nova\Resources;

use Laravel\Nova\Resource;
use Illuminate\Http\Request;
use DKulyk\Scheduler\Entities\ScheduleLog;
use RabbitCMS\Modules\Concerns\BelongsToModule;
use Laravel\Nova\Fields\{Code, DateTime, ID, Select, Status, Text};

/**
 * Class LogResource.
 * @property-read ScheduleLog $resource
 */
class LogResource extends Resource
{
    use BelongsToModule;

    public static $model = ScheduleLog::class;

    public static $globallySearchable = false;

    public static $displayInNavigation = false;

    public static function uriKey(): string
    {
        return 'scheduler-log';
    }

    public function authorizedToDelete(Request $request): bool
    {
        return false;
    }

    public function authorizedToUpdate(Request $request): bool
    {
        return false;
    }

    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }

    public function fields(Request $request): array
    {
        $module = self::module();

        return [
            ID::make(),

            Status::make($module->trans('scheduler.Status'), 'status')
                ->failedWhen([$module->trans('scheduler.Error')])
                ->loadingWhen([$module->trans('scheduler.Pending')])
                ->displayUsing(function () use ($module) {
                    switch ($this->resource->status) {
                        case 0:
                            return $module->trans('scheduler.Pending');
                        case 1:
                            return $module->trans('scheduler.Finished');
                        case 2:
                            return $module->trans('scheduler.Error');
                    }

                    return $this->resource->status;
                }),

            DateTime::make($module->trans('scheduler.Started'), 'started_at'),

            DateTime::make($module->trans('scheduler.Stopped'), 'stopped_at'),

            Code::make($module->trans('scheduler.Error'), 'exception')
                ->hideFromIndex(),
        ];
    }
}
