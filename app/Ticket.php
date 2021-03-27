<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_number', 'draw_date', 'draw_time', 'customer_name'
    ];

    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function cancellation()
    {
        return $this->hasOne(Cancellation::class);
    }

}
