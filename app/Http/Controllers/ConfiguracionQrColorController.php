<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionQrColor;
use App\Util\LogErrorManager;
use App\Util\ResultManager;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ConfiguracionQrColorController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $this->getFilters($request, new ConfiguracionQrColor());
        $perpage = $this->getLimitPagination($request);

        $query = ConfiguracionQrColor::where($filters)
            ->orderBy('orden', 'ASC')
            ->orderBy('idconfig', 'ASC');

        $configuraciones = $query->paginate($perpage);

        return $configuraciones;
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
            $configuracion = $this->setModel(new ConfiguracionQrColor(), $request);
            $configuracion->userinsert = $this->user->email;
            $configuracion->save();

            $result = ResultManager::genericSuccessMessage();
        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::genericFailMessage();
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::genericFailMessage();
        }

        return response()->json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $configuracion = ConfiguracionQrColor::findOrFail($id);
            return response()->json($configuracion);
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            return response()->json(['error' => 'Configuración no encontrada'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $configuracion = ConfiguracionQrColor::findOrFail($id);
            return response()->json($configuracion);
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            return response()->json(['error' => 'Configuración no encontrada'], 404);
        }
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
        $result;
        try {
            $configuracion = ConfiguracionQrColor::findOrFail($id);
            $configuracion = $this->setModel($configuracion, $request);
            $configuracion->userupdate = $this->user->email;
            $configuracion->save();

            $result = ResultManager::genericSuccessMessage();
        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::genericFailMessage();
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::genericFailMessage();
        }

        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result;
        try {
            $configuracion = ConfiguracionQrColor::findOrFail($id);
            $configuracion->estado = 'I';
            $configuracion->userupdate = $this->user->email;
            $configuracion->save();

            $result = ResultManager::genericSuccessMessage();
        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::genericFailMessage();
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::genericFailMessage();
        }

        return response()->json($result);
    }

    /**
     * Mapea los datos del request al modelo
     *
     * @param  ConfiguracionQrColor  $configuracion
     * @param  \Illuminate\Http\Request  $request
     * @return ConfiguracionQrColor
     */
    private function setModel(ConfiguracionQrColor $configuracion, Request $request): ConfiguracionQrColor
    {
        $configuracion->anio_egreso = $request->anio_egreso;
        $configuracion->es_beca18 = $request->es_beca18;
        $configuracion->color = $request->color;
        $configuracion->descripcion = $request->descripcion;
        $configuracion->orden = $request->orden;

        return $configuracion;
    }
}
