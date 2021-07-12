<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'from_user_wallet_id',
        'to_user_wallet_id',
        'from_character',
        'to_character',
        'from_value',
        'to_value',
        'type_oper',
        'from_quotation',
        'to_quotation'
    ];
}
