<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fingagent extends Model
{
    protected $fillable = ['merchantLoginId','merchantLoginPin','merchantFName','merchantMName','merchantLName','merchantAddress','merchantCityName','merchantState','merchantPhoneNumber','merchantEmail','merchantShopName','userPan','merchantPinCode','merchantAadhar', 'aadharPic','pancardPic','status','everify','via','user_id','remark','father','dob','thana','merchantalernativeNumber','passport','shoppic','companyBankAccountNumber','bankIfscCode'];

    public $appends = ['username'];
    
    public function getUsernameAttribute()
    {
        $data = '';
        if($this->user_id){
            $user = \App\Models\User::where('id' , $this->user_id)->first(['name', 'id', 'role_id']);
            $data = $user->name." (".$user->id.") <br>(".$user->role->name.")";
        }
        return $data;
    }
}
