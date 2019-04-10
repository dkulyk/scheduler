<?php

declare(strict_types=1);

namespace DKulyk\Scheduler;

/**
 * Class Scheduler.
 */
final class Scheduler
{
    private $jobs = [];

    /**
     * @param  string  $job
     * @return $this
     */
    public function registerJob(string $job): self
    {
        $this->jobs[$job] = $job;

        return $this;
    }

    /**
     * @return array
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }
}
