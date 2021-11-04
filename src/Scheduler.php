<?php

declare(strict_types=1);

namespace DKulyk\Scheduler;

use Carbon\Carbon;
use DKulyk\Scheduler\Jobs\ScheduleJob;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Application;
use Illuminate\Queue\InteractsWithQueue;
use DKulyk\Scheduler\Entities\{Schedule, ScheduleLog};
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

final class Scheduler
{
    private array $jobs = [];

    public function __construct(protected Application $application)
    {
    }

    public function registerJob(string $job): self
    {
        $this->jobs[$job] = $job;

        return $this;
    }

    public function getJobs(): array
    {
        return $this->jobs;
    }

    public function run(Schedule $schedule): mixed
    {
        $job = $this->application->make($schedule->job, ['options' => $schedule->options]);

        $log = $schedule->logs()->create([
            'started_at' => Carbon::now(),
            'status' => 0,
        ]);

        if ($job instanceof ShouldQueue && in_array(Batchable::class, class_uses_recursive($job))) {
            $batch = Bus::batch([$job]);
        } else {
            $batch = Bus::batch([new ScheduleJob($job)]);
        }

        if (! empty($job->queue)) {
            $batch->onQueue($job->queue);
        }

        if (! empty($job->connection)) {
            $batch->onConnection($job->connection);
        }

        $batch
            ->allowFailures()
            ->name("schedule:{$log->id}")
            ->then([self::class, 'complete'])
            ->catch([self::class, 'catch']);

        return $batch->dispatch();
    }

    public static function complete(Batch $batch): void
    {
        if (preg_match('/^schedule:(\d+)$/', $batch->name, $matches)) {
            ScheduleLog::query()
                ->find($matches[1])
                ?->update([
                    'status' => 1,
                    'stopped_at' => Carbon::now(),
                ]);
        }
    }

    public static function catch(Batch $batch, $exception): void
    {
        if (preg_match('/^schedule:(\d+)$/', $batch->name, $matches)) {
            ScheduleLog::query()
                ->find($matches[1])
                ?->update([
                    'status' => 2,
                    'stopped_at' => Carbon::now(),
                    'exception' => (string) $exception,
                ]);
        }
    }
}
