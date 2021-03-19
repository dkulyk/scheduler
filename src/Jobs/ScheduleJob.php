<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Jobs;

use Illuminate\Bus\{Batchable, Dispatcher, Queueable};
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

final class ScheduleJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Batchable;
    use SerializesModels;
    use Queueable;

    public int $tries = 0;

    public function __construct(private $command)
    {
    }

    public function handle(Dispatcher $dispatcher): mixed
    {
        return $dispatcher->dispatchNow($this->command);
    }

    public function displayName(): string
    {
        return get_class($this->command);
    }

    public function middleware(): array
    {
        return array_merge(
            method_exists($this->command, 'middleware') ? $this->command->middleware() : [],
            $this->command->middleware ?? []
        );
    }
}
