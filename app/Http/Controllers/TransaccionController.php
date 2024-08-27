<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TransaccionController extends Controller
{
    public function deposito(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $numeroTarjeta = $request->input('numeroTarjeta');
                $monto = $request->input('monto');

                DB::statement('CALL RegistrarDeposito(?, ?)', [$numeroTarjeta, $monto]);
            });

            return response()->json(['message' => 'Depósito realizado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function Retiro(Request $request)
    {
        try {
            $numeroTarjeta = $request->input('numeroTarjeta');
            $monto = $request->input('monto');

            DB::statement('CALL RegistrarRetiro(?, ?)', [$numeroTarjeta, $monto]);

            return response()->json(['message' => 'Retiro realizado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function Transferencia(Request $request)
    {
        try {
            $numeroTarjetaEmisora = $request->input('numeroTarjetaEmisora');
            $numeroTarjetaReceptora = $request->input('numeroTarjetaReceptora');
            $monto = $request->input('monto');

            DB::statement('CALL RegistrarTransferencia(?, ?, ?)', [$numeroTarjetaEmisora, $numeroTarjetaReceptora, $monto]);

            return response()->json(['message' => 'Transferencia realizada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function ObtenerHistorial(Request $request)
    {
        try {
            $numerosTarjetas = $request->query('numerosTarjetas');

            $historialTransacciones = DB::select('CALL ObtenerHistorialTransacciones(?)', [$numerosTarjetas]);

            if (empty($historialTransacciones)) {
                return response()->json(['message' => 'No se encontraron transacciones para las tarjetas proporcionadas.'], 404);
            }

            // Formatear los resultados con las claves en formato camelCase
            $historialFormateado = array_map(function ($transaccion) {
                return [
                    'numeroTarjetaEmisora' => $transaccion->NumeroTarjetaEmisora ?? null,
                    'numeroTarjetaReceptora' => $transaccion->NumeroTarjetaReceptora,
                    'monto' => $transaccion->Monto,
                    'descripcionTransaccion' => $transaccion->DescripcionTransaccion,
                ];
            }, $historialTransacciones);

            return response()->json($historialFormateado);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }




    public function TransferenciaInterBancaria(Request $request)
    {
        $validated = $request->validate([
            'numeroTarjetaEmisora' => 'required|string|max:16',
            'numeroTarjetaReceptora' => 'required|string|max:16',
            'monto' => 'required|numeric|min:0',
        ]);

        try {
            DB::statement('CALL TransferenciaInterBancaria(?, ?, ?)', [
                $validated['numeroTarjetaEmisora'],
                $validated['numeroTarjetaReceptora'],
                $validated['monto']
            ]);

            $response = Http::post('http://localhost:5238/api/Transaccion/RecibirTransferenciaInterBancaria', [
                'numeroTarjetaEmisora' => $validated['numeroTarjetaEmisora'],
                'numeroTarjetaReceptora' => $validated['numeroTarjetaReceptora'],
                'monto' => $validated['monto']
            ]);

            if ($response->successful()) {
                return response()->json(['message' => 'Transferencia realizada correctamente'], 200);
            } else {
                // Manejar los errores de la API externa
                return response()->json([
                    'error' => 'Error al realizar la transferencia',
                    'details' => $response->json()
                ], $response->status());
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    
    public function RecibirTransferenciaInterBancaria(Request $request)
    {
        $validated = $request->validate([
            'NumeroTarjetaEmisora' => 'required|string|max:16',
            'NumeroTarjetaReceptora' => 'required|string|max:16',
            'Monto' => 'required|numeric|min:0',
        ]);

        try {
            Log::info('RecibirTransferenciaInterBancaria llamada con datos: ', $validated);

            DB::statement('CALL RegistrarTransferencia(?, ?, ?)', [
                $validated['NumeroTarjetaEmisora'],
                $validated['NumeroTarjetaReceptora'],
                $validated['Monto']
            ]);

            return response()->json(['message' => 'Transferencia realizada correctamente'], 200);
        } catch (\Exception $e) {
            Log::error('Error en RecibirTransferenciaInterBancaria: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }



}
