<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'url',
        'admin_path',
        'platform_type',
        'magento_version',
        'repository_url',
        'contact_info',
        'developer_info',
        'has_cpanel',
        'has_root_access',
        'last_check',
    ];

    /**
     * Get the client that owns the store.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the modules for the store.
     */
    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    /**
     * Get the server info for the store.
     */
    public function serverInfo()
    {
        return $this->hasOne(ServerInfo::class);
    }

    /**
     * Get the store stats for the store.
     */
    public function storeStat()
    {
        return $this->hasOne(StoreStat::class);
    }

    /**
     * Get the security statuses for the store.
     */
    public function securityStatuses()
    {
        return $this->hasMany(StoreSecurityStatus::class);
    }
}
