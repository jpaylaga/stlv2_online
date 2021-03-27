<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditHistory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 'type', 'user_id', 'from', 'to', 'amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
   
}
