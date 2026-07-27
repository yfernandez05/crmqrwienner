<?php

namespace App\Http\Controllers;
use App\Exports\ClientesExport;
use App\Exports\plantillaCliente;
use App\Exports\plantillaExportMatricula;
use App\Mail\MessageReceived;
use App\Models\Campania;
use App\Models\Cliente;
use App\Models\EventoCliente;
use App\Models\PaginaDinamica;
use App\Util\LogErrorManager;
use App\Util\ResultManager;
use App\Util\RuleManager;
use App\Util\SedeHelper;
use App\Util\SignedUrlUtils;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\ImageManagerStatic as Image;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpKernel\Exception\HttpException;
use ZipArchive;

class ClienteController extends BaseController
{
    public function __construct()
    {
        parent::__construct(['index', 'urlcontrato','showViewRegisterCliente', 'saveClientQr']);
    }

    /**
     * Muestra la vista de registro de cliente con página dinámica
     *
     * @return \Illuminate\Http\Response
     */
    public function showViewRegisterCliente()
    {
        $pagina = PaginaDinamica::select('titulo', 'slug', 'descripcion', 'idcampania', 'api_token')
            ->where([
                ['slug', 'registro'],
                ['estado_general', 'A']
            ])
            ->first();

        if (!$pagina) {
            abort(404, 'Página no encontrada o inactiva.');
        }

        return view('registercliente', compact('pagina'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $this->getFilters($request, new Cliente());
        //agregamos el modelo EVENTO adicionales en cliente + evento, hace el filtro de busqueda en Cliente + Evento
        $filtersEvento = $this->getFilters($request, new EventoCliente());
        $filterCampania = $this->getFilters($request, new Campania());
        $perpage = $this->getLimitPagination($request);

        if($request->idevento)
        {
            $querycliente = Cliente::where($filters)
            ->with('eventoCliente','campania','distrito','ultimaatencion','tiporecordatorio')
            //agregamos filtro adicionales
            ->whereHas('eventoCliente', function ($query) use ($filtersEvento) {
                $query->where($filtersEvento);
            }) /*
            ->whereHas('evento')  */;

            if ($this->user->idcuenta != RuleManager::SYSTEM_COMPANY_ACCOUNT || !in_array($this->user->idrol, RuleManager::ADMINISTRATORS_ACCESS)) {
                //filtro mostrar usuarios que pertenecen a una cuenta
                $querycliente->whereHas('campania', function ($query) {
                    $query->where('idcuenta', '=', $this->user->idcuenta);
                });
            }

            if($request->exists('keyimportado')){
                $querycliente->whereNotNull('uuidimportacion');
            }

            if ($request->exists('fechadesde')) {
                $fechadesde = Carbon::createFromFormat('d-m-Y', $request->fechadesde)->toDateString();
                $querycliente->whereDate('fecharegistro', '>=', $fechadesde);
            }

            if ($request->exists('fechahasta')) {
                $fechahasta = Carbon::createFromFormat('d-m-Y', $request->fechahasta)->toDateString();
                $querycliente->whereDate('fecharegistro', '<=', $fechahasta);
            }

            $cliente = $querycliente->orderBy('idcliente', 'DESC')
            ->paginate($perpage);

        }else{

        $querycliente = Cliente::where($filters)
            ->with('eventoCliente','campania','distrito','ultimaatencion','tiporecordatorio')
            //agregamos filtro adicionales
            ->whereHas('campania', function ($query) use ($filterCampania) {
                $query->where($filterCampania);
            }) /*
            ->whereHas('evento')  */;

            if ($this->user->idcuenta != RuleManager::SYSTEM_COMPANY_ACCOUNT || !in_array($this->user->idrol, RuleManager::ADMINISTRATORS_ACCESS)) {
                //filtro mostrar usuarios que pertenecen a una cuenta
                $querycliente->whereHas('campania', function ($query) {
                    $query->where('idcuenta', '=', $this->user->idcuenta);
                });
            }

            if($request->exists('keyimportado')){
                $querycliente->whereNotNull('uuidimportacion');
            }

            if ($request->has('celinvalid')) {
                $querycliente->where('idtipoatencion', '!=', 3);
            }

            if ($request->exists('fechadesde')) {
                $fechadesde = Carbon::createFromFormat('d-m-Y', $request->fechadesde)->toDateString();
                $querycliente->whereDate('fecharegistro', '>=', $fechadesde);
            }

            if ($request->exists('fechahasta')) {
                $fechahasta = Carbon::createFromFormat('d-m-Y', $request->fechahasta)->toDateString();
                $querycliente->whereDate('fecharegistro', '<=', $fechahasta);
            }

            $cliente = $querycliente->orderBy('idcliente', 'DESC')
            /*cambiar moemntaneo*/
            /*->where('idcampania','=',10)*/
            ->paginate($perpage);
        }
        return $cliente;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = "";
        try {
            $cliente = new Cliente();
            $cliente = $this->setModel($cliente, $request);
            $cliente->userinsert = $this->user->email;
            $cliente->save();

            $result = ResultManager::genericSuccessMessage();

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);

            $duplicateEntry = 1062; // registro duplicado

            if (count($e->errorInfo)) {
                if ($e->errorInfo[1] == $duplicateEntry) {
                    $result = ResultManager::warningMessage('La dirección de correo electrónico que ha ingresado ya está registrada.');
                } else {
                    $result = ResultManager::errorMessage('Intente nuevamente más tarde.');
                }

            } else {
                $result = ResultManager::errorMessage('Intente nuevamente más tarde.');
            }

        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);

            $result = ResultManager::gerericErrorMessage();
        }

        return $result;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //return Cliente::with('campania', 'distrito')->find($id);
        return Cliente::with('eventoCliente','campania', 'distrito')
        ->whereHas('campania')->find($id);

        // return Cliente::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $result = "";
        try {

            $cliente = $this->setModel(Cliente::findOrFail($id), $request);
            $cliente->userupdate = $this->user->email;
            $cliente->update();

            if($request->idevento != 0){
                EventoCliente::updateOrCreate(
                    ['idcliente' => $id],
                    ['idevento' => $request->idevento],
                );
            }else{
                EventoCliente::destroy($id);
            }

            $result = ResultManager::genericSuccessMessage();

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);

            $result = ResultManager::errorMessage('Es posible que haya un alumno registrado con el mismo nombre.');

        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);

            $result = ResultManager::gerericErrorMessage();
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = "";

        try {
                $cliente = Cliente::findOrFail($id);
                $cliente->estado = $cliente->estado == RuleManager::DISABLED_STATE ? RuleManager::ACTIVE_STATE : RuleManager::DISABLED_STATE;
                $cliente->userupdate = $this->user->email;
                $cliente->update();

                if ($cliente->estado == RuleManager::ACTIVE_STATE) {
                    $result = ResultManager::successMessage('Participante restaurado correctamente.');
                } else if($cliente->estado == RuleManager::DISABLED_STATE) {
                    $result = ResultManager::warningMessage('Participante eliminado correctamente.');
                }

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::gerericErrorMessage();

        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::gerericErrorMessage();
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function physicallyRemove(Request $request)
    {
        $result = "";

        $id = $request->id;
        try {

            Cliente::destroy($id);
            $result = ResultManager::successMessage('Alumno eliminado correctamente.');

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::gerericErrorMessage();

        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::gerericErrorMessage();
        }

        return $result;
    }

    private function setModel(Cliente $cliente, Request $request): Cliente
    {

        $cliente->nombres = $request->nombres;
        $cliente->apellidopaterno = $request->apellidopaterno;
        $cliente->apellidomaterno = $request->apellidomaterno;
        $cliente->telefono = $request->telefono;
        $cliente->prefijo = $request->prefijo;
        $cliente->carrera = $request->carrera;
        $cliente->grado = $request->grado;
        $cliente->colegio = $request->has('sede') ? $request->sede : $request->colegio;
        $cliente->sede_destino = $request->sede_destino;
        $cliente->email = $request->email;
        $cliente->idcampania = $request->idcampania;
        $cliente->procedencia = $request->input('procedencia') ?: 'Orgánico';
        $cliente->utm = $request->utm;
        $cliente->campaign_content = $request->campaign_content;
        $cliente->campaign_medium = $request->campaign_medium;
        $cliente->campaign_name = $request->campaign_name;
        $cliente->campaign_source = $request->campaign_source;
        $cliente->campaign_term = $request->campaign_term;
        $cliente->grado = $request->grado;
        $cliente->coddistrito = $request->coddistrito;
        $cliente->tipodocumento = $request->tipodocumento;
        $cliente->dni = $request->dni;
        $cliente->anioegreso = $request->anioegreso;
        $cliente->autorizo_uso_imagen = $request->has('autorizo_uso_imagen') ? (int)$request->autorizo_uso_imagen : 0;
        $cliente->es_padre_familia = $request->es_padre_familia;

        return $cliente;
    }

    public function exportarExcel(Request $request)
    {
        try {
            $name = 'Alumnos[' . Carbon::now('America/Lima')->format('d-m-Y H:i:s') . ' ].xlsx';
            return Excel::download(new ClientesExport($request), $name);

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);

            throw new HttpException(500);
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);

            throw new HttpException(500);
        }
    }

    public function descargarplantilla(Request $request)
    {
        try {
            $name = 'plantillaAlumno[' . Carbon::now('America/Lima')->format('d-m-Y H:i:s') . ' ].xlsx';
            return Excel::download(new plantillaCliente($request), $name);

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);

            throw new HttpException(500);
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);

            throw new HttpException(500);
        }
    }

    public function descargarplantillamatricula(Request $request)
    {
        try {
            $name = 'plantillaMatricula[' . Carbon::now('America/Lima')->format('d-m-Y H:i:s') . ' ].xlsx';
            return Excel::download(new plantillaExportMatricula($request), $name);

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);

            throw new HttpException(500);
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);

            throw new HttpException(500);
        }
    }

    public function sedesUni(){
        $sedes = [
             ["sedesalumno" => 'Campus Villa'],
             ["sedesalumno" => 'Campus Norte'],
             ["sedesalumno" => 'Campus Ate'],
             ["sedesalumno" => 'Campus Aramburu'],
        ];
        return $sedes;
    }

    public function procedenciaAdsUtm(){
        $procedencia = [
             ["procedencia" => 'Orgánico'],
             ["procedencia" => 'Pauta'],
        ];
        return $procedencia;
    }

    public function saveClientQr(Request $request)
    {
        $result = '';

        DB::beginTransaction();
        try {

            $cliente = new Cliente();
            $cliente = $this->setModel($cliente, $request);
            $cliente->userinsert = $this->user ? $this->user->email : 'registro_publico';
            $cliente->save();

            $this->enviarCorreoConQR($cliente, false);

            $result = ResultManager::successMessage('Los datos ingresados se guardaron correctamente.');

            DB::commit();

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            DB::rollback();

            $duplicateEntry = 1062; // registro duplicado

            // Log temporal para debug
            \Log::error('QueryException en saveClientQr: ' . $e->getMessage());
            \Log::error('Error Info: ' . json_encode($e->errorInfo));

            if (count($e->errorInfo)) {
                if ($e->errorInfo[1] == $duplicateEntry) {
                    $result = ResultManager::warningMessage('La dirección de correo electrónico que ha ingresado ya está registrada.');
                } else {
                    $result = ResultManager::errorMessage('Error en base de datos: ' . $e->getMessage());
                }

            } else {
                $result = ResultManager::errorMessage('Ocurrió un error inesperado. Intente nuevamente más tarde');
            }

        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            DB::rollback();

            // Log temporal para debug
            \Log::error('Exception en saveClientQr: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            $result = ResultManager::errorMessage('Error general: ' . $e->getMessage());
        }
        return $result;
    }

    private function enviarCorreoConQR($cliente,$issendmail)
    {
        $datos = [
            "nombre" => $cliente->nombres,
            "correo" => $cliente->email,
            "idcampania" => $cliente->idcampania,
        ];

        // Convertir los datos a formato JSON
        $datosJSON = json_encode($datos);

        // "Cifrar" los datos utilizando base64_encode()
        $datosCifrados = base64_encode($datosJSON);

        // Generar código QR con los datos "cifrados"
        $qrCodePNG = QrCode::format('png')->size(400)->generate($datosCifrados); //prod
        $qqWithPadding = $this->paddinQRpng($qrCodePNG, 50); //prod

        //$qrCodePNG = QrCode::size(400)->generate($datosCifrados); //local
        //$qqWithPadding = $this->agregarPaddingSVG($qrCodePNG, '20px'); //local

        if (!$issendmail){
            Mail::to($cliente->email)->send(new MessageReceived($cliente, $qqWithPadding));
        }

        if($issendmail){
            return $qqWithPadding;
        }

    }

    function paddinQRpng($img, $padding){

        if ($img instanceof HtmlString) {
            $img = (string) $img;
        }

        $qrImage = Image::make($img);

        // Calcular el tamaño del lienzo con padding
        $canvasWidth = $qrImage->width() + 2 * $padding;
        $canvasHeight = $qrImage->height() + 2 * $padding;

        // Crear un lienzo blanco
        $canvas = Image::canvas($canvasWidth, $canvasHeight, '#ffffff');

        $canvas->insert($qrImage, 'center');

        return $canvas->encode('png');

    }


    function agregarPaddingSVG($svgString, $padding) {
        return str_replace('<svg', '<svg style="padding: ' . $padding . '; background: #fff;"', $svgString);
    }

    public function donwloadQR(Request $request)
    {
        try {
            $filters = $this->getFilters($request, new Cliente());
            $filterCampania = $this->getFilters($request, new Campania());

            $querycliente = Cliente::select('idcliente','nombres', 'email', 'idcampania','fecharegistro')
                ->where($filters)
                ->with('eventoCliente','campania','distrito')
                ->whereHas('campania', function ($query) use ($filterCampania) {
                    $query->where($filterCampania);
                });

            if ($this->user->idcuenta != RuleManager::SYSTEM_COMPANY_ACCOUNT || !in_array($this->user->idrol, RuleManager::ADMINISTRATORS_ACCESS)) {
                $querycliente->whereHas('campania', function ($query) {
                    $query->where('idcuenta', '=', $this->user->idcuenta);
                });
            }

            if($request->exists('keyimportado')){
                $querycliente->whereNotNull('uuidimportacion');
            }

            if ($request->exists('fechadesde')) {
                $fechadesde = Carbon::createFromFormat('d-m-Y', $request->fechadesde)->toDateString();
                $querycliente->whereDate('fecharegistro', '>=', $fechadesde);
            }

            if ($request->exists('fechahasta')) {
                $fechahasta = Carbon::createFromFormat('d-m-Y', $request->fechahasta)->toDateString();
                $querycliente->whereDate('fecharegistro', '<=', $fechahasta);
            }

            $clientedata = $querycliente->orderBy('idcliente', 'DESC')->get();

            // Carpeta temporal para almacenar los códigos QR antes de comprimir
            $tempFolder = storage_path('app/public/temp_qrcodes');

            // Verificar y crear la carpeta temporal si no existe
            if (!file_exists($tempFolder)) {
                mkdir($tempFolder, 0777, true);
            }

            // Generar y almacenar los códigos QR para cada cliente
            foreach ($clientedata as $cliente) {
                if (!is_null($cliente->nombres) && !is_null($cliente->email) && !is_null($cliente->idcampania)) {

                    /* $qrCode = QrCode::size(400)->generate($qrDataJson); */

                    $nombreArchivo = $cliente->email . '_' . $cliente->idcliente . '.png'; //prod
                    //$nombreArchivo = $cliente->email . '_' . $cliente->idcliente . '.svg';

                    // Almacenar el código QR en la carpeta temporal
                    Storage::put('public/temp_qrcodes/' . $nombreArchivo, (string) $this->enviarCorreoConQR($cliente, true));

                }
            }

            // Crear un archivo ZIP para los códigos QR
            $zipFileName = 'qrcodes_' . date('Y-m-d_H-i-s') . '.zip';
            $zip = new ZipArchive;
            if ($zip->open(storage_path('app/public/') . $zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                // Agregar archivos a ZIP
                $files = Storage::files('public/temp_qrcodes');
                foreach ($files as $file) {
                    $fileName = basename($file);
                    $zip->addFile(storage_path('app/') . $file, $fileName);
                }

                // Cerrar y guardar en el ZIP
                $zip->close();

                // Eliminar los archivos temporales de códigos QR
                Storage::deleteDirectory('public/temp_qrcodes');
            }

            // Ruta completa del archivo ZIP
            $pathToFile = storage_path('app/public/') . $zipFileName;

            // Descargar el archivo ZIP y eliminarlo después de enviarlo
            $result = response()->download($pathToFile)->deleteFileAfterSend(true);


        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            //dd($e);
            $result = ResultManager::errorMessage('Error al generar los datos del QR.');
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            //dd($e);
            $result = ResultManager::errorMessage('Error al procesar la solicitud.');
        }

        return $result;
    }

    public function downloadSingleQR(Request $request)
    {
        $result = null;
        try {
            $idcliente = $request->input('idcliente');

            // Validar el email del cliente
            if (!$idcliente) {
                return ResultManager::errorMessage('no se puede generar el QR por falta del correo.');
            }

            // Obtener el cliente por email
            $cliente = Cliente::select('idcliente','nombres', 'email', 'idcampania', 'fecharegistro')
                ->where('idcliente', $idcliente)
                ->first();

            // Validar que se encontró el cliente
            if (is_null($cliente)) {
                return ResultManager::errorMessage('No se pudo identificar el registro al generar el QR.');
            }

            // Generar el código QR
            $qrContent = (string) $this->enviarCorreoConQR($cliente, true);

            // Nombre del archivo
            $fileName = $cliente->email . '_' . $cliente->idcliente . '.png'; //prod
            //$fileName = $cliente->email . '_' . $cliente->idcliente . '.svg'; //local

            // Retornar la respuesta con el archivo
            $result = response($qrContent, 200)
                ->header('Content-Type', 'image/png') //prod
                //->header('Content-Type', 'image/svg+xml') //local
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            dd($e);
            $result = ResultManager::errorMessage('Error al generar los datos del QR.');
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            dd($e);
            $result = ResultManager::errorMessage('Error al procesar la solicitud.');
        }

        return $result;

    }

    public function generatecard($id){
        $cliente = Cliente::find($id);

        $qryf =  $this->enviarCorreoConQR($cliente, true);

        //$qrCodeDataUri = 'data:image/svg+xml;base64,' . base64_encode($qryf);//local
        //$pdf = Pdf::loadView('card.cardaccess', ['cliente' => $cliente, 'qryf' => $qrCodeDataUri]); //local

        $qrCodeDataUri = 'data:image/png;base64,' . base64_encode($qryf);//prod
        $pdf = Pdf::loadView('card.cardaccess', ['cliente' => $cliente, 'qryf' => $qrCodeDataUri]); //prod

        return $pdf->stream('Carnet - '.$cliente->nombres.' '.$cliente->apellidopaterno.' '.$cliente->apellidomaterno  . '.pdf');
    }

    public function selectsearch(Request $request)
    {
        $authcampania = Auth()->user()->idcampania;
        $search = $request->input('search');

        $query = Cliente::where([['idcampania',$authcampania],['estado','A']])
            ->select('idcliente', 'email', 'nombres', 'apellidopaterno','apellidomaterno','telefono','asistencia', 'idcampania', 'fechaasistencia','ismatricula')
            ->whereRaw("concat(email,' ',telefono, ' ',nombres,' ',apellidopaterno,' ',apellidomaterno) like ?", "%{$search}%")
            ->with('eventoCliente')
            ->orderBy('idcliente', 'DESC')
            ->limit(15) // Limitar el número de resultados
            ->get();

        return $query;
    }

    public function updateAutorizacionImagen(Request $request)
    {
        $cliente = Cliente::find($request->idcliente);
        if ($cliente) {
            $cliente->autorizo_uso_imagen = ($request->autorizo_uso_imagen == '1' || $request->autorizo_uso_imagen === 1) ? 1 : 0;
            try {
                $cliente->save();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al guardar']);
            }
            $fresh = Cliente::find($cliente->idcliente);
            return response()->json(['guardado' => $fresh->autorizo_uso_imagen]);
        }
        return ResultManager::errorMessage('Cliente no encontrado.');
    }


    public function reenviarCorreo($id)
    {
        $result = '';

        DB::beginTransaction();
        try {

            $cliente = Cliente::findOrFail($id);
            $this->enviarCorreoConQR($cliente, false);
            $result = ResultManager::successMessage('Correo reenviado correctamente.');
            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::errorMessage('No se pudo reenviar el correo, intente nuevamente.');
        }

        return $result;
    }


    public function urlcontrato($id)
    {
        $urlConFirma = SignedUrlUtils::generateSignedUrl('cliente.descargatuentrada', ['id' => $id]);
        return response()->json(['urlpdfqracceso' => $urlConFirma]);
    }

   public function pdfclienteevento($id)
    {

        $currentUrl = request()->url();
        $expectedUrlDescarga = route('cliente.descargatuentrada', ['id' => $id]);

        if ($currentUrl === $expectedUrlDescarga) {
            $signature = request()->query('signature');
            if (!$signature) {
                abort(403, 'Firma ausente');
            }

            $urlSinFirma = route('cliente.descargatuentrada', [
                'id' => $id,
            ], false); // "false"

            $key = env('APP_KEY');
            if (Str::startsWith($key, 'base64:')) {
                $key = base64_decode(Str::after($key, 'base64:'));
            }

            $firmaCalculada = hash_hmac('sha256', $urlSinFirma, $key);

            if (!hash_equals($firmaCalculada, $signature)) {
                abort(403, 'Firma no válida');
            }
        }

        $cliente = Cliente::findOrFail($id);

        $qryf = $this->enviarCorreoConQR($cliente, true);
        $qrCodeDataUri = 'data:image/svg+xml;base64,' . base64_encode($qryf);//local


        $cliente->loadMissing('campania');
        $sedeInfo = SedeHelper::getSedeInfo($cliente->colegio, $cliente->campania);

        $pdf = Pdf::loadView('qrclientes.qrclientes_invitados', [
            'cliente' => $cliente,
            'sedeInfo' => $sedeInfo,
            'qryf' => $qrCodeDataUri
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('qr-acceso-' . $cliente->nombres .' '. $cliente->apellidopaterno. '.pdf');
    }

}
