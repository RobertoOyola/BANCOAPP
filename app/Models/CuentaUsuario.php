<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaUsuario extends Model
{
    protected $table = 'cuenta_usuario';
    
    protected $primaryKey = 'ID_CUENTA_USUARIO';
    
    public $incrementing = false;
    
    protected $fillable = [
        'ID_USUARIO',
        'CORREO',
        'CONTRASENIA',
        'ESTADO'
    ];

    public $timestamps = false;
}
