<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';
    
    protected $primaryKey = 'ID_USUARIO';
    
    public $incrementing = false;
    
    protected $fillable = [
        'NOMBRES',
        'APELLIDOS',
        'ESTADO'
    ];

    public $timestamps = false;
}
