<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Betlimit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'combination', 'type', 'limit', 'winning'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
