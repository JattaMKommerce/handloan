<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationBalance extends Model
{
    protected $fillable = ['api_id','baseurl','method','requesttype','username','password','balance','status','success','failed', 'usernameval', 'passwordval','responsetype','other'];
}
