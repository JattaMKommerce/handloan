<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationCircle extends Model
{
    protected $fillable = ['circle_id', 'api', 'code'];

    public $appends = ['apis', 'codes', 'circlename'];

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

    public function getCirclenameAttribute()
    {
        $data = '';
        if($this->circle_id){
            $provider = Circle::where('id' , $this->circle_id)->first(['state']);
            $data = $provider->state;
        }
        return $data;
    }
}
