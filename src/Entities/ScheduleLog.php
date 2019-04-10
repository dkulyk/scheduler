<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ScheduleLog.
 */
final class ScheduleLog extends Model
{
    public const CREATED_AT = 'started_at';
    public const UPDATED_AT = null;

    protected $table = 'scheduler_log';

    protected $fillable = [
        'started_at',
        'stopped_at',
        'status',
        'exception',
    ];

    protected $dates = [
        'started_at',
        'stopped_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id')->withoutTrashed();
    }
}
