<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationOperator extends Model
{
    protected $fillable = ['provider_id', 'api', 'code'];
    public $appends = ['apis', 'codes', 'providername'];

    public function setApiAttribute($value)
    {
        $this->attributes['api'] = implode(",", $value);
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = implode(",", $value);
    }

    public function getApisAttribute()
    {
        return \App\Models\Integration::whereIn('id' , explode(",", $this->api))->get(['name', 'id']);;
    }

    public function getCodesAttribute()
    {
        return explode(",", $this->code);
    }

    public function getProvidernameAttribute()
    {
        $data = '';
        if($this->provider_id){
            $provider = Provider::where('id' , $this->provider_id)->first(['name']);
            $data = $provider->name;
        }
        return $data;
    }
}
