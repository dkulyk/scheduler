<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Nova\Resources;

use DKulyk\Scheduler\Entities\ScheduleLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\{Code, DateTime, ID, Select, Status, Text};
use Laravel\Nova\Resource;
use RabbitCMS\Modules\Concerns\BelongsToModule;

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

    /**
     * @return string
     */
    public static function uriKey()
    {
        return 'scheduler-log';
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
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
                        case 1 :
                            return $module->trans('scheduler.Finished');
                        case 2 :
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
