<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'name',
        'version',
        'is_active',
    ];

    /**
     * Get the store that owns the module.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
