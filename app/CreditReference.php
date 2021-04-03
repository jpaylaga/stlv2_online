<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditReference extends Model
{

    protected $table = 'references';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
   
}
