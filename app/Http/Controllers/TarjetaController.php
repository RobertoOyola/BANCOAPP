<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarjetaController extends Controller
{
    public function registrarTarjeta(Request $request)
    {
        // Validar los datos recibidos
        $validated = $request->validate([
            'idTipoCuenta' => 'required|integer',
            'saldo' => 'required|numeric',
            'idCuentaUsuario' => 'required|integer',
        ]);

        try {
            // Llamar al procedimiento almacenado
            DB::statement('CALL banco.RegistrarTarjeta(?, ?, ?)', [
                $validated['idTipoCuenta'],
                $validated['saldo'],
                $validated['idCuentaUsuario']
            ]);

            return response()->json(['message' => 'Tarjeta registrada con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al registrar la tarjeta: ' . $e->getMessage()], 500);
        }
    }

    public function obtenerTarjetas(Request $request)
{
    $cuentaUsuario = $request->input('cuentaUsuario');

    // Llamada al procedimiento almacenado
    $tarjetas = DB::select('CALL ObtenerTarjetas(?)', [$cuentaUsuario]);

    // Mapear el resultado a objetos de tipo TarjetaDTO
    $tarjetasDTO = array_map(function ($tarjeta) {
        return [
            'numeroTarjeta' => $tarjeta->NUMERO_TARJETA,
            'descripcionCuenta' => $tarjeta->descripcionCuenta,
            'saldo' => $tarjeta->SALDO
        ];
    }, $tarjetas);

    // Retornar los datos como JSON
    return response()->json($tarjetasDTO);
}



}
