<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

  

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    // use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','email','mobile','password','remember_token','nsdlwallet','api_token','lockedamount','aepslockedamount','role_id','parent_id','company_id','scheme_id','status','address','shopname','gstin','city','state','pincode','pancard','aadharcard','pancardpic','aadharcardpic','gstpic','profile','kyc','callbackurl','remark','resetpwd','otpverify','otpresend','account','bank','ifsc','bene_id1','apptoken','agntpic','signature','shop_photo','livepic','passwordold'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    protected static $logAttributes = ['id','name','email','mobile','password','scheme_id','status','address','shopname','gstin','city','state','pincode','pancard','aadharcard','callbackurl','otpverify','otpresend','account','bank','ifsc','apptoken'];

    protected static $logOnlyDirty = true;

    public $with = ['role', 'company'];
    protected $appends = ['parents'];
   
    public function role()
    {
        
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function company(){
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
    
    public function getParentsAttribute() {
        $user = User::where('id', $this->parent_id)->first(['id', 'name', 'mobile', 'role_id']);
        if($user){
            return $user->name." (".$user->id.")<br>".$user->mobile."<br>".$user->role->name;
        }else{
            return "Not Found";
        }
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }
}
