<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityPatch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'magento_version',
        'patch_name',
        'release_date',
        'type',
        'severity_score',
        'severity_level',
        'description',
    ];

    /**
     * Get the security statuses associated with the patch.
     */
    public function securityStatuses()
    {
        return $this->hasMany(StoreSecurityStatus::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'release_date' => 'date',
        ];
    }
}
