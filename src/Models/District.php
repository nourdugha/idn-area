<?php

namespace zaidysf\IdnArea\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property string $code
 * @property string $regency_code
 * @property string $name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class District extends Model
{
    use HasFactory;

    protected $table = 'idn_districts';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'code',
        'regency_code',
        'name',
    ];

    protected $casts = [
        'code' => 'string',
        'regency_code' => 'string',
        'name' => 'string',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \zaidysf\IdnArea\Database\Factories\DistrictFactory
    {
        return \zaidysf\IdnArea\Database\Factories\DistrictFactory::new();
    }

    /**
     * Get the regency that owns the district.
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
            'regency_code',  // Local key on districts table
            'province_code'  // Local key on regencies table
        );
    }

    /**
     * Get all villages that belong to this district.
     */
    public function villages(): HasMany
    {
        return $this->hasMany(Village::class, 'district_code', 'code');
    }
}
