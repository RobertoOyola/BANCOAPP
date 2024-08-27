<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\TarjetaController;
use App\Http\Controllers\TransaccionController;

Route::group(['middleware' => 'api'], function () {
    Route::post('/LoginUsuario', [UsuarioController::class, 'login']);
    Route::post('/RegistrarUsuario', [UsuarioController::class, 'registrarUsuario']);

    Route::post('/RegistrarTarjeta', [TarjetaController::class, 'registrarTarjeta']);
    Route::get('/ObtenerTarjetas', [TarjetaController::class, 'obtenerTarjetas']);
    Route::post('/Deposito', [TransaccionController::class, 'deposito']);
    Route::post('/Retiro', [TransaccionController::class, 'Retiro']);
    Route::post('/Transferencia', [TransaccionController::class, 'Transferencia']);
    Route::post('/TransferenciaInterBancaria', [TransaccionController::class, 'TransferenciaInterBancaria']);
    Route::post('/RecibirTransferenciaInterBancaria', [TransaccionController::class, 'RecibirTransferenciaInterBancaria']);
    Route::get('/ObtenerHistorial', [TransaccionController::class, 'ObtenerHistorial']);
});