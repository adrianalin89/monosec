<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSecurityStatus extends Model
{
    use HasFactory;

    protected $table = 'store_security_status';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'security_patch_id',
        'is_applied',
        'risk_score',
        'notes',
    ];

    /**
     * Get the store that owns the security status.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the security patch associated with this status.
     */
    public function securityPatch()
    {
        return $this->belongsTo(SecurityPatch::class);
    }
}
