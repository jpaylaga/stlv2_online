<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\CreditHistory;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'firstname', 'lastname', 'percentage', 'float', 'facebook', 'credits', 'activate_credit', 'email', 'password', 'role', 'phone', 'id_number', 'picture', 'dob', 'gender', 'civil_status', 'address'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'pivot', 'updated_at', 'email_verified_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->belongsToMany(Profile::class);
    }

    public function children()
    {
        return $this->belongsToMany('App\User', 'child_user', 'user_id', 'child_id');
    }

    public function parent()
    {
        return $this->belongsToMany('App\User', 'child_user', 'child_id', 'user_id');
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function areas()
    {
        return $this->belongsToMany(Area::class);
    }

    public function agentAreas()
    {
        return $this->belongsToMany(Area::class, 'agent_area');
    }

    public function creditHistories()
    {
        return $this->belongsToMany(CreditHistory::class);
    }

    public function creditBalance(){
        $deposits = CreditHistory::where('to', $this->id)
            ->where('type', 'deposit')
            ->sum('amount');
        $withdraws = CreditHistory::where('to', $this->id)
            ->where('type', 'withdraw')
            ->sum('amount');

        return $deposits - $withdraws;
    }

    public function creditLogs($type = '', $limit = 20){

        $histories_query = CreditHistory::where('from', $this->id)
            ->orWhere('to', $this->id);

        if($type)
            $histories_query->where('credit_histories.type', $type);

        $histories = $histories_query
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($hist) {
                $by = User::find($hist->user_id);
                $hist->by = $by->firstname.' '.$by->lastname;
                return $hist;
            });
        
        return $histories;
    }
}
