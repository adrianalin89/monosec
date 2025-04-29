<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'company_name',
        'api_key',
        'has_credentials',
    ];

    /**
     * Get the stores for the client.
     */
    public function stores()
    {
        return $this->hasMany(Store::class);
    }
}
