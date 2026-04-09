<?php

namespace App\Http\Controllers;

use App\Models\PaginaDinamica;
use App\Util\LogErrorManager;
use App\Util\ResultManager;
use App\Util\RuleManager;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class PaginaDinamicaController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $this->getFilters($request, new PaginaDinamica());
        $perpage = $this->getLimitPagination($request);

        $queryData = PaginaDinamica::where($filters)->with('campania');

        $query = $queryData->orderBy('idpagina', 'DESC')
            ->paginate($perpage);

        return $query;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return PaginaDinamica::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $result = "";
        try {

            $paginaDinamica = $this->setModel(PaginaDinamica::findOrFail($id), $request);
            $paginaDinamica->userupdate = $this->user->email;
            $paginaDinamica->update();

            $result = ResultManager::genericSuccessMessage();

        } catch (QueryException $e) {

            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::errorMessage('Es posible que hay una pagina externa registrada con el mismo nombre.');

        } catch (Exception $e) {

            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::gerericErrorMessage();
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaginaDinamica $paginaDinamica)
    {
        //
    }

    public function toggle(Request $request, $id)
    {
        $result = '';

        try {

            $paginaDinamica = $this->setModel(PaginaDinamica::findOrFail($id), $request);
            $paginaDinamica->userupdate = $this->user->email;

            $paginaDinamica->estado_general = $paginaDinamica->estado_general === RuleManager::ACTIVE_STATE
                ? RuleManager::DISABLED_STATE
                : RuleManager::ACTIVE_STATE;
            $paginaDinamica->update();

            if ($paginaDinamica->estado_general == RuleManager::ACTIVE_STATE) {
                $result = ResultManager::successMessage('Enlace activado correctamente.');
            }
            if ($paginaDinamica->estado_general == RuleManager::DISABLED_STATE) {
                $result = ResultManager::warningMessage('Enlace desactivado correctamente.');
            }
        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::errorMessage('Error al actualizar el estado de mantenimiento.');
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::gerericErrorMessage();
        }

        return $result;
    }

    private function setModel(PaginaDinamica $paginaDinamica, Request $request): PaginaDinamica
    {
        $paginaDinamica->slug = $request->slug ?? $paginaDinamica->slug;
        $paginaDinamica->titulo = $request->titulo ?? $paginaDinamica->titulo;
        $paginaDinamica->descripcion = $request->descripcion ?? $paginaDinamica->descripcion;
        $paginaDinamica->idcampania = $request->idcampania ?? $paginaDinamica->idcampania;

        return $paginaDinamica;
    }

    public function regenerarToken($id)
    {
        $result = "";

        try {
            $paginaDinamica = PaginaDinamica::findOrFail($id);

            // Generar token simple
            $newToken = 'wiener_' . bin2hex(random_bytes(16)) . '_' . time();

            $paginaDinamica->api_token = $newToken;
            $paginaDinamica->userupdate = $this->user->email;
            $paginaDinamica->save();

            $result = ResultManager::successMessage('Token generado correctamente', ['token' => $newToken]);
        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::gerericErrorMessage();
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::gerericErrorMessage();
        }

        return $result;
    }
}
