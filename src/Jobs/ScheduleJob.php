<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Jobs;

use Carbon\Carbon;
use DateInterval;
use DKulyk\Scheduler\Entities;
use Illuminate\Bus\Dispatcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

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
            $result = $dispatcher->dispatchNow($application->make($this->schedule->job, $this->schedule->options));

            $log->update([
                'status' => 1,
                'stopped_at' => Carbon::now(),
                'exception' => json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
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
