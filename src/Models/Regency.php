<?php

namespace zaidysf\IdnArea\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property string $code
 * @property string $province_code
 * @property string $name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Regency extends Model
{
    use HasFactory;

    protected $table = 'idn_regencies';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'code',
        'province_code',
        'name',
    ];

    protected $casts = [
        'code' => 'string',
        'province_code' => 'string',
        'name' => 'string',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \zaidysf\IdnArea\Database\Factories\RegencyFactory
    {
        return \zaidysf\IdnArea\Database\Factories\RegencyFactory::new();
    }

    /**
     * Get the province that owns the regency.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    /**
     * Get all districts that belong to this regency.
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'regency_code', 'code');
    }

    /**
     * Get all villages that belong to this regency through districts.
     */
    public function villages(): HasManyThrough
    {
        return $this->hasManyThrough(
            Village::class,
            District::class,
            'regency_code', // Foreign key on districts table
            'district_code', // Foreign key on villages table
            'code',         // Local key on regencies table
            'code'          // Local key on districts table
        );
    }

    /**
     * Get all islands that belong to this regency.
     */
    public function islands(): HasMany
    {
        return $this->hasMany(Island::class, 'regency_code', 'code');
    }
}
