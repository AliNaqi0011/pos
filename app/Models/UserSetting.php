<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'twillio_sid',
        'twillio_phone', 
        'twillio_auth_token',
        'two_fa_type',
        'stripe_payment_method',
        'stripe_secret_key',
        'stripe_publishable_key',
        'app_name',
        'app_logo',
        'app_favicon',
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'company_website',
        'tax_number',
        'currency',
        'timezone',
        'date_format',
        'time_format',
        'fbr_enabled',
        'fbr_pos_id',
        'fbr_username',
        'fbr_password',
        'fbr_api_url'
    ];

    public function user()
    {
       return $this->belongsTo(User::class,'user_id', 'id');
    }
}
