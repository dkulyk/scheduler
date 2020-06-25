<?php

declare(strict_types=1);

namespace DKulyk\Scheduler\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\{Collection, Model, Relations\HasMany, SoftDeletes};
use DKulyk\Eloquent\Extensions\Concerns\HasEnabled;
use DKulyk\Scheduler\Support\CryptOptions;

/**
 * Class Schedule.
 * @property-read int $id
 * @property string|null $schedule
 * @property string|null $event
 * @property string $job
 * @property bool $enabled
 * @property string $delay
 * @property array $options
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon|null $deleted_at
 *
 * @property-read ScheduleLog[]|Collection $logs
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
        'enabled' => false,
    ];

    protected $attributes = [
        'options' => '{}',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(ScheduleLog::class, 'schedule_id');
    }
}
