<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\TipoRecordatorio;
use App\Util\LogErrorManager;
use App\Util\ResultManager;
use App\Util\RuleManager;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class TipoRecordatorioController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $this->getFilters($request, new TipoRecordatorio());
        $perpage = $this->getLimitPagination($request);

        $query = TipoRecordatorio::where($filters)
            ->where('idtiporecordatorio','>=',RuleManager::SYSTEM_ADMIN_ROLE)
            ->orderBy('idtiporecordatorio','DESC')
            ->paginate($perpage);

        return $query;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $result = "";
        try {
            $tipoAtencion = $this->setModel(new TipoRecordatorio(),$request);
            $tipoAtencion->save();

            $result = ResultManager::genericSuccessMessage();
        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this,__FUNCTION__, $e);

            $result = ResultManager::errorMessage('Es posible que haya un Tipo de Recordatorio registrado con el mismo nombre.');

        } catch (Exception $e){
            LogErrorManager::saveInDB($this,__FUNCTION__, $e);

            $result = ResultManager::gerericErrorMessage();
        }
        return $result;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return TipoRecordatorio::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $result = "";
        try {
            $query = $this->setModel(TipoRecordatorio::findOrFail($id), $request);
            $query->save();

            $result = ResultManager::genericSuccessMessage();
        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);

            $result = ResultManager::errorMessage('Es posible que haya un Tipo de Recordatorio registrado con el mismo nombre.');

        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);

            $result = ResultManager::gerericErrorMessage();
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = "";
        $errorMsg='No se puede eliminar. Hay un Cliente que hace uso de este Tipo de Atencion.';
        try {
        $numberDependents = Cliente::where(['idtiporecordatorio'=>$id,'estado'=> RuleManager::ACTIVE_STATE])->count();

         if($numberDependents == 0){

            $tiporecordatorio = TipoRecordatorio::findOrFail($id);
            $tiporecordatorio->estado=RuleManager::DISABLED_STATE;
            $tiporecordatorio->update();

            $result = ResultManager::successMessage('Tipo de Recordatorio eliminado correctamente.');
         }else{
             $result=ResultManager::errorMessage($errorMsg);
         }

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::errorMessage($errorMsg);

        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::gerericErrorMessage();
        }

        return $result;
    }

    private function setModel(TipoRecordatorio $tiporecordatorio, Request $request): TipoRecordatorio
    {
        $tiporecordatorio->tiporecordatorio = $request->tiporecordatorio;
        $tiporecordatorio->backgroundColor = $request->backgroundColor;
        $tiporecordatorio->textColor = $request->textColor;
        return $tiporecordatorio;
    }

    public function select(Request $request)
    {
        $filters = $this->getFilters($request, new TipoRecordatorio());

        $queryTipoRecordatorio = TipoRecordatorio::where($filters);

        if ($request->exists('istosave')){
            $queryTipoRecordatorio->where('idtiporecordatorio','>',RuleManager::UNATTENDED_STATE_TYPE);
        }

        $tiporecordatorios = $queryTipoRecordatorio->orderBy('idtiporecordatorio', 'DESC')->get();

        return $tiporecordatorios;

    }
}
