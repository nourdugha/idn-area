<?php

namespace zaidysf\IdnArea\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $code
 * @property string $name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Province extends Model
{
    use HasFactory;

    protected $table = 'idn_provinces';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'code',
        'name',
    ];

    protected $casts = [
        'code' => 'string',
        'name' => 'string',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \zaidysf\IdnArea\Database\Factories\ProvinceFactory
    {
        return \zaidysf\IdnArea\Database\Factories\ProvinceFactory::new();
    }

    /**
     * Get all regencies that belong to this province.
     */
    public function regencies(): HasMany
    {
        return $this->hasMany(Regency::class, 'province_code', 'code');
    }

    /**
     * Get all districts that belong to this province through regencies.
     */
    public function districts(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            District::class,
            Regency::class,
            'province_code', // Foreign key on regencies table
            'regency_code',  // Foreign key on districts table
            'code',          // Local key on provinces table
            'code'           // Local key on regencies table
        );
    }

    /**
     * Get all villages that belong to this province through regencies and districts.
     * Note: This is a heavy query, use with caution.
     */
    public function villages(): Builder
    {
        return Village::whereHas('district.regency', function ($query) {
            $query->where('province_code', $this->code);
        });
    }
}
