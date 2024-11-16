<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Investment extends Model
{
    use HasFactory;
    protected $table = 'investments';
    protected $fillable = ['banner_id', 'status', 'start_date', 'end_date','totalNoOfShare', 'mature_amount', 'maturity_at', 'amount', 'user_id','title','video_id'];

    protected $with = ['banner', 'invested','video'];
    public function banner()
    {
       return $this->belongsTo(Banner::class, 'banner_id', 'id');
    }

    public function video()
    {
       return $this->belongsTo(Video::class, 'video_id', 'id');
    }

    public function invested()
    {
       return $this->hasOne(InvestmentTxn::class, 'id','investment_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }

}
