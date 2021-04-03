<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{

    use HasApiTokens;

    protected $fillable = [
        'name',
        'street',
        'barangay',
        'city',
        'zipcode',
        'province',
        'is_affiliated',
        'is_active',
        'area_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function area()
    {
        return $this
            ->belongsTo(Area::class);
    }

}
