<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CreditHistory;

class CreditRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ref',
        'type', 
        'status', 
        'outlet',
        'amount',
        'description', 
        'user_id', 
        'player_id', 
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function updateUserCredit(){
        $user = auth()->id();
        $to = $this->player;
        $credits = $this->amount;
        $type = $this->type;

        if( $type == 'deposit' )
            $to->credits += $this->amount;
        else
            $to->credits -= $this->amount;

        if( $to->save() ){
            CreditHistory::create([
                'type' => $type,
                'to' => $to->id,
                'from' => $user,
                'amount' => $credits,
                'user_id' => $user,
            ]);
            return true;
        }

        return false;
    }

}
