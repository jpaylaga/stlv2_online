<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function draw()
    {
        return $this->belongsTo(Game::class);
    }

}
