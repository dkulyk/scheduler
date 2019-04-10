<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Entities;

use DKulyk\Eloquent\Extensions\Concerns\HasEnabled;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Schedule
 * @package DKulyk\Scheduler\Entities
 * @property-read int $id
 * @property string|null $schedule
 * @property string|null $event
 * @property string $job
 * @property bool $enabled
 * @property string $delay
 * @property array $options
 * @property-read \Carbon\Carbon $created_at
 * @property-read \Carbon\Carbon $updated_at
 * @property-read \Carbon\Carbon|null $deleted_at
 *
 * @property-read \DKulyk\Scheduler\Entities\ScheduleLog[]|\Illuminate\Database\Eloquent\Collection $logs
 */
final class Schedule extends Model
{
    use SoftDeletes;
    use HasEnabled;

    protected $table = 'scheduler';

    protected $fillable = [
        'caption',
        'schedule',
        'job',
        'delay',
    ];

    protected $casts = [
        'options' => 'json',
    ];

    protected $attributes = [
        'options' => '{}',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ScheduleLog::class, 'schedule_id');
    }
}

