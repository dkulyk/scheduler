<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Jobs;

use DateInterval;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use DKulyk\Scheduler\Entities;
use Illuminate\Bus\Dispatcher;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class ScheduleJob.
 */
final class ScheduleJob implements ShouldQueue
{
    use SerializesModels;
    use Queueable;

    /**
     * @var \DKulyk\Scheduler\Entities\Schedule
     */
    private $schedule;

    /**
     * ScheduleJob constructor.
     * @param  \DKulyk\Scheduler\Entities\Schedule  $schedule
     * @throws \Exception
     */
    public function __construct(Entities\Schedule $schedule)
    {
        $this->schedule = $schedule;

        if (! empty($schedule->delay)) {
            $this->delay = new DateInterval("P{$schedule->delay}");
        }
    }

    /**
     * @param  \Illuminate\Bus\Dispatcher  $dispatcher
     * @param  \Illuminate\Contracts\Foundation\Application  $application
     * @throws \Throwable
     */
    public function handle(Dispatcher $dispatcher, Application $application)
    {
        $log = $this->schedule->logs()->create([
            'started_at' => Carbon::now(),
            'status' => 0,
        ]);
        try {
            $dispatcher->dispatchNow($application->make($this->schedule->job, $this->schedule->options));

            $log->update([
                'status' => 1,
                'stopped_at' => Carbon::now(),
            ]);
        } catch (\Throwable $exception) {
            $log->update([
                'status' => 2,
                'stopped_at' => Carbon::now(),
                'exception' => (string) $exception,
            ]);

            throw $exception;
        }
    }
}
