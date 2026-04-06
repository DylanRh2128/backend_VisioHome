<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';

    public $incrementing = false;
    protected $primaryKey = 'correo';
    protected $keyType = 'string';

    const UPDATED_AT = null;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'correo',
        'token',
        'created_at'
    ];
}
