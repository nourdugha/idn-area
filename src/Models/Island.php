<?php

namespace zaidysf\IdnArea\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property string|null $code
 * @property string|null $coordinate
 * @property string $name
 * @property bool $is_outermost_small
 * @property bool $is_populated
 * @property string|null $regency_code
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Island extends Model
{
    use HasFactory;

    protected $table = 'idn_islands';

    protected $fillable = [
        'code',
        'coordinate',
        'name',
        'is_outermost_small',
        'is_populated',
        'regency_code',
    ];

    protected $casts = [
        'code' => 'string',
        'coordinate' => 'string',
        'name' => 'string',
        'is_outermost_small' => 'boolean',
        'is_populated' => 'boolean',
        'regency_code' => 'string',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \zaidysf\IdnArea\Database\Factories\IslandFactory
    {
        return \zaidysf\IdnArea\Database\Factories\IslandFactory::new();
    }

    /**
     * Get the regency that owns the island.
     */
    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'regency_code', 'code');
    }

    /**
     * Get the province through regency.
     */
    public function province(): HasOneThrough
    {
        return $this->hasOneThrough(
            Province::class,
            Regency::class,
            'code',          // Foreign key on regencies table
            'code',          // Foreign key on provinces table
            'regency_code',  // Local key on islands table
            'province_code'  // Local key on regencies table
        );
    }

    /**
     * Scope a query to only include outermost small islands.
     */
    public function scopeOutermostSmall(Builder $query): Builder
    {
        return $query->where('is_outermost_small', true);
    }

    /**
     * Scope a query to only include populated islands.
     */
    public function scopePopulated(Builder $query): Builder
    {
        return $query->where('is_populated', true);
    }

    /**
     * Scope a query to only include unpopulated islands.
     */
    public function scopeUnpopulated(Builder $query): Builder
    {
        return $query->where('is_populated', false);
    }
}
