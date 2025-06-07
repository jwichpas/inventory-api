<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCompanie extends Model
{
    protected $table = 'user_companies';

    protected $fillable = [
        'user_id',
        'empresa_id'
    ];

    /* public $timestamps = false; */
}
