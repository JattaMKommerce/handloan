<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class flight_txn_details extends Model
{
    use HasFactory;

    protected $table = "flight_txn_details";

    public $with = ['user'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public $appends = ['username'];
    
    public function getUsernameAttribute()
    {
        $data = '';
        if($this->user_id){
            $user = \App\User::where('id' , $this->user_id)->first(['name', 'id', 'role_id']);
            if($user){
            $data = $user->name." (".$user->id.") <br>(".$user->role->name.")";
            }
        }
        return $data;
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i:s A', strtotime($value));
    }

}
