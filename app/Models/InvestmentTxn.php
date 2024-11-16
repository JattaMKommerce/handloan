<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class InvestmentTxn extends Model
{
    protected $table = 'investment_txns';
    protected $fillable = ['investment_id', 'status', 'amount', 'user_id'];

    protected $with = ['investedscheme'];

    public function investedscheme()
    {
        return $this->belongsTo(Investment::class, 'investment_id', 'id');
    }

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }

}