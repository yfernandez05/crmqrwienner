<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Importacion;
use App\Util\ResultManager;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Util\LogErrorManager;
use App\Imports\ClientesImport;
use App\Models\Cliente;
use App\Models\Evento;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;

class ImportacionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $this->getFilters($request, new Importacion());
        $perpage = $this->getLimitPagination($request);

        $queryImportaciones = Importacion::where($filters)
            ->with('user');
            

        if ($request->exists('fechadesde')){
            $fechadesde = Carbon::createFromFormat('d-m-Y', $request->fechadesde)->toDateString();
            $queryImportaciones->whereDate('fecharegistro','>=', $fechadesde);
        }

        if ($request->exists('fechahasta')) {
            $fechahasta = Carbon::createFromFormat('d-m-Y', $request->fechahasta)->toDateString();           
            $queryImportaciones->whereDate('fecharegistro','<=', $fechahasta);
        }

        $importaciones = $queryImportaciones-> orderBy('idimportacion', 'DESC')
            ->paginate($perpage);

        return $importaciones;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result;
        try {
            
            $file = $request->file('file');

            $importacion = new Importacion();
            $importacion->nombrearchivo = $file->getClientOriginalName(); 
            $importacion->fecharegistro = Carbon::now();
            $importacion->iduser = $this->user->id; 
            $importacion->uuidimportacion = Str::uuid();
            $importacion->idcampania = $request->idcampania;

            $evento = $request->idevento;

            $clientImport = new ClientesImport($importacion, $evento);
            Excel::import($clientImport, $file);

            
            // Verificar errores
            if (!empty($clientImport->errors)) {
                $errorMessages = '';
                foreach ($clientImport->errors as $error) {
                    $errorMessages .= 'Fila ' . $error['row'] . ': ' . $error['message'] . "</br>";
                }
                return ResultManager::warningMessage("<span class='text-dark font-weight-bold'>Registros no importados corregir en:</span></br> <span class='text-dark'>" . $errorMessages);
            }
            
            $importacion->cantregistros = $clientImport->getRowCount(); 
            $importacion->save();

            $result = ResultManager::genericSuccessMessage();
        
         } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);

            $duplicateEntry = 1062; // registro duplicado

            if (count($e->errorInfo)) {
                if ($e->errorInfo[1] == $duplicateEntry) {
                    $result = ResultManager::warningMessage('No se puede importar datos duplicados en el sistema, verficar el <strong>email</strong>.');
                } else {
                    $result = ResultManager::errorMessage('Intente nuevamente más tarde.');
                }

            } else {
                $result = ResultManager::errorMessage('Intente nuevamente más tarde.');
            }

        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            //return dd($e);
            $result = ResultManager::gerericErrorMessage();
        }

        return $result;
    }

    public function storematricula(Request $request)
    {
        $result = '';
        try {
            $file = $request->file('file');

            $importacion = new Importacion();
            $importacion->nombrearchivo = $file->getClientOriginalName(); 
            $importacion->fecharegistro = Carbon::now();
            $importacion->iduser = $this->user->id; 
            $importacion->uuidimportacion = Str::uuid();
            $importacion->idcampania = $request->idcampania;

            $data = Excel::toArray([], $file)[0];
            
            $actualizados = [];

            foreach (array_slice($data, 1) as $row) {
                $email = $row[0] ?? null;
                $celular = $row[1] ?? null;

                //dump($email, $celular);

                if ($email || $celular) {
                    $clientes = Cliente::where('idcampania', $request->idcampania)
                        ->where(function ($query) use ($email, $celular) {
                            $query->where('email', $email)
                                  ->orWhere('telefono', $celular);
                        })
                        ->where('ismatricula', '!=', 1)
                        ->whereNull('uuidmatriculaimportacion')
                        ->get();
                        
                    if (count($clientes) > 0) {
                        foreach ($clientes as $cliente) {
                            $cliente->ismatricula = 1;
                            $cliente->uuidmatriculaimportacion = Str::uuid();
                            $cliente->save();
                            $actualizados[] = $cliente->idcliente;
                        }
                    }
                }
            }

            $importacion->cantregistros = count($actualizados);
            //dump($importacion);
            $importacion->save();

            $result = ResultManager::successMessage("Importación completada con " . count($actualizados) . " registros matriculados.");

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::errorMessage('Intente nuevamente más tarde.');
            dd($e);

        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::gerericErrorMessage();
            dd($e);
        }

        return $result;
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
