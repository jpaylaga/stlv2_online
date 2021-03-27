<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'val',
        'type',
        'field_type',
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
