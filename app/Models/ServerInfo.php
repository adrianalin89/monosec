<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerInfo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'server_info'; // Explicitly define table name

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'os_info',
        'php_version',
        'composer_version',
        'redis_version',
        'opensearch_version',
        'mariadb_version',
        'rabbitmq_version',
        'other_info',
    ];

    /**
     * Get the store that owns the server info.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
