<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apiswitch extends Model
{
    use HasFactory;

    protected $fillable = ['value', 'api_id', 'provider_id', 'user_id', 'type','action'];

    public function api(){
        return $this->belongsTo('App\Model\Api');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

    public $appends = ['apiname', 'username', 'providername', 'stateval', 'users'];

    public function getUsernameAttribute()
    {
        $data = '';
        if($this->user_id){
            $user = \App\User::where('id' , $this->user_id)->first(['name', 'id', 'role_id']);
            $data = @$user->name." (".@$user->id.") <br>(".@$user->role->name.")";
        }
        return $data;
    }

    public function getUsersAttribute()
    {
        return explode(",", $this->user_id);
    }

    public function getApinameAttribute()
    {
        $data = Api::where('id' , $this->api_id)->first(['product']);
        if($data){
            return $data->product;
        }else{
            return "None";
        }
    }

    public function getProvidernameAttribute()
    {
        $data = Provider::where('id' , $this->provider_id)->first(['name']);
        if($data)
        {
        return $data->name;
        }else{
            return '';
        }
    }

    public function getStatevalAttribute()
    {
        if($this->type == "state"){
            $data = Circle::where('id' , $this->value)->first();
                
            if($data){
                return $data->state ?? $this->value;
            }else{
                return $this->value;
            }
        }else{
            return $this->value;
        }
    }
}
