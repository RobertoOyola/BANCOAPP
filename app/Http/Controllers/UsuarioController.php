<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'correo' => 'required|email',
            'contrasenia' => 'required',
        ]);

        $correo = $request->input('correo');
        $contrasenia = $request->input('contrasenia');

        // Ejecutar el procedimiento almacenado
        $result = DB::select('CALL LoginUsuario(?, ?, @pIdCuenta, @pNombres, @pApellidos)', [$correo, $contrasenia]);

        // Obtener los valores de salida
        $cuenta = DB::select('SELECT @pIdCuenta AS idCuenta, @pNombres AS nombres, @pApellidos AS apellidos');

        if (is_null($cuenta[0]->idCuenta)) {
            return response()->json(['message' => 'Datos incorrectos'], 404);
        }

        return response()->json([
            'idCuenta' => $cuenta[0]->idCuenta,
            'nombres' => $cuenta[0]->nombres,
            'apellidos' => $cuenta[0]->apellidos,
        ]);
    }


    public function registrarUsuario(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'correo' => 'required|string|email|max:100',
            'contrasenia' => 'required|string|max:100',
        ]);

        $nombres = $request->input('nombres');
        $apellidos = $request->input('apellidos');
        $correo = $request->input('correo');
        $contrasenia = $request->input('contrasenia');

        // Ejecutar el stored procedure
        DB::statement('CALL RegistrarUsuario(?, ?, ?, ?)', [$nombres, $apellidos, $correo, $contrasenia]);

        return response()->json(['message' => 'Usuario creado con éxito'], 200);
    }
}
