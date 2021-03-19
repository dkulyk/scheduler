<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Jobs;

use DateInterval;
use Carbon\Carbon;
use DKulyk\Scheduler\Entities\Schedule;
use Illuminate\Bus\Queueable;

use Illuminate\Bus\Dispatcher;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Foundation\Application;
use Throwable;

/**
 * Class ScheduleJob.
 */
final class ScheduleJob implements ShouldQueue
{
    use SerializesModels;
    use Queueable;

    public $tries = 1;

    private Schedule $schedule;

    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;

        if (! empty($schedule->delay)) {
            $this->delay = new DateInterval("P{$schedule->delay}");
        }
    }

    public function handle(Dispatcher $dispatcher, Application $application): void
    {
        $log = $this->schedule->logs()->create([
            'started_at' => Carbon::now(),
            'status' => 0,
        ]);

        try {

            $result = $dispatcher->dispatchNow($application->make($this->schedule->job,
                ['options' => $this->schedule->options]));

            $log->update([
                'status' => 1,
                'stopped_at' => Carbon::now(),
                'exception' =>
                    json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ]);
        } catch (Throwable $exception) {
            $log->update([
                'status' => 2,
                'stopped_at' => Carbon::now(),
                'exception' => (string) $exception,
            ]);

            throw $exception;
        }
    }

    public function displayName(): string
    {
        return $this->schedule->job;
    }
}
