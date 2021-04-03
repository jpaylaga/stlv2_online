<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'address', 'cutoff_time', 'logo', 'limit'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'pivot'
    ];

    
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function outlets()
    {
        return $this->hasMany(Outlet::class);
    }

    public function areaAgents()
    {
        return $this->belongsToMany(User::class, 'agent_area');
    }
}
