<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class BarredKeywords implements Rule
{
    protected $barredKeywords;

    public function __construct()
    {
        $this->barredKeywords = [
            'PVT LTD', 
            'ENTERPRISES', 
            'COMPANY', 
            'DAIRY', 
            'BUSINESS', 
            'SERVICES', 
            'CREDIT FINANCE PVT LTD'
        ];
    }

    public function passes($attribute, $value)
    {
        foreach ($this->barredKeywords as $keyword) {
            if (stripos($value, $keyword) !== false) {
                return false;
            }
        }
        return true;
    }

    public function message()
    {
        return 'The :attribute contains a barred keyword.';
    }
}
