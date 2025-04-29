<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreStat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'customer_count',
        'order_count',
    ];

    /**
     * Get the store that owns the stats.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
