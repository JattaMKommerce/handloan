<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationComplain extends Model
{
    protected $fillable = ['api_id','baseurl','method','requesttype','username','password','mobile','operator','amount','txnid','payid','refno','message','status','success','pending','failed', 'usernameval', 'passwordval','responsetype','other'];
}
