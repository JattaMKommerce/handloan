<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationCallback extends Model
{
    protected $fillable = ['api_id','baseurl','txnid','refno','payid','rechargeid','message','status','success','pending','failed','type'];
}
