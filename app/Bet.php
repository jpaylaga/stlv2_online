<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'combination', 
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

}
