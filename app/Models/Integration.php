<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    // use HasFactory;
    protected $fillable = ['name','baseurl','method','requesttype','username','password','mobile','extraparam','operator','amount','txnid','state','refno','payid','message','status','success','pending','failed', 'usernameval', 'passwordval','responsetype','other', 'type','headerval1','headerval2','header1','header2','balance'];

}
