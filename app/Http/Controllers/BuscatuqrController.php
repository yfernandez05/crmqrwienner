<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\PaginaDinamica;
use App\Services\QRService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class BuscatuqrController extends Controller
{
    protected $qrService;

    public function __construct(QRService $qrService)
    {
        $this->qrService = $qrService;
    }

    /**
     * Muestra la vista principal de búsqueda de QR
     */
    public function index()
    {
        $slugPage = PaginaDinamica::where('slug', 'buscatuqr')
            ->where('estado_general', 'A')
            ->first();

        if (!$slugPage) {
            abort(404, 'Página no encontrada o inactiva.');
        }

        return view('buscatuqr');
    }

    /**
     * Busca cliente y genera QR
     */
    public function search(Request $request)
    {
        try {
            $email = $request->email;
            $telefono = $request->telefono;

            // Validar que al menos uno esté presente
            if (!$telefono && !$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe ingresar un correo o teléfono.'
                ], 400);
            }

            // Verificar que la página está activa
            $slugPage = PaginaDinamica::select('slug', 'idcampania', 'estado')
                ->where('slug', 'buscatuqr')
                ->where('estado_general', 'A')
                ->first();

            if (!$slugPage) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró resultado con la información ingresada.'
                ], 404);
            }

            // Buscar cliente (búsqueda insensible a mayúsculas/minúsculas)
            $cliente = Cliente::query()
                ->select('idcliente', 'idcampania', 'nombres', 'apellidomaterno',
                         'apellidopaterno', 'email', 'telefono', 'carrera',
                         'ismatricula', 'colegio', 'coddistrito', 'anioegreso', 'fecharegistro')
                ->when($telefono, fn($q) => $q->where('telefono', '=', $telefono))
                ->when($email, fn($q) => $q->whereRaw('LOWER(email) = ?', [strtolower($email)]))
                ->where('estado', 'A')
                ->when($slugPage->idcampania, fn($q) => $q->where('idcampania', $slugPage->idcampania))
                ->first();

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró resultado con la información ingresada.'
                ], 404);
            }

            // Preparar datos para el QR
            $datos = [
                //'idcliente' => $cliente->idcliente,
                'nombre'    => $cliente->nombres,
                'correo'    => $cliente->email,
                "idcampania" => $cliente->idcampania,

            ];

            // Generar QR (sin enviar correo)
            $qrContent = $this->qrService->generateQRAndSendmail($datos, false);
            $qrBase64 = base64_encode($qrContent);

            // Preparar respuesta con datos del cliente
            $dataresult = [
                'cliente' => $cliente,
                'qr' => 'data:image/png;base64,' . $qrBase64,
            ];

            return response()->json([
                'success' => true,
                'message' => 'PARTICIPANTE VALIDADO',
                'dataaditional' => $dataresult
            ]);

        } catch (QueryException $e) {
            \Log::error('Error QueryException en BuscatuqrController::search - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al consultar los datos.'
            ], 500);
        } catch (Exception $e) {
            \Log::error('Error Exception en BuscatuqrController::search - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado. Por favor intente nuevamente.'
            ], 500);
        }
    }
}
