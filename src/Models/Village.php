<?php

namespace zaidysf\IdnArea\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property string $code
 * @property string $district_code
 * @property string $name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Village extends Model
{
    use HasFactory;

    protected $table = 'idn_villages';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'code',
        'district_code',
        'name',
    ];

    protected $casts = [
        'code' => 'string',
        'district_code' => 'string',
        'name' => 'string',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): \zaidysf\IdnArea\Database\Factories\VillageFactory
    {
        return \zaidysf\IdnArea\Database\Factories\VillageFactory::new();
    }

    /**
     * Get the district that owns the village.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_code', 'code');
    }

    /**
     * Get the regency through district.
     */
    public function regency(): HasOneThrough
    {
        return $this->hasOneThrough(
            Regency::class,
            District::class,
            'code',         // Foreign key on districts table
            'code',         // Foreign key on regencies table
            'district_code', // Local key on villages table
            'regency_code'  // Local key on districts table
        );
    }

    /**
     * Get the province through district and regency.
     */
    public function province(): ?Province
    {
        return Province::whereHas('regencies.districts', function ($query) {
            $query->where('code', $this->district_code);
        })->first();
    }
}
