<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class settingModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'accountId',
        'tokenMs',

        'urlOrganization',
        'AUTH_RSA256',
        'RSA256',
        'GOSTKNCA',

        'tin',
        'Username',
        'Password',
    ];

}
