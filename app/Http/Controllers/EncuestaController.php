<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use App\Models\PaginaDinamica;
use App\Models\Campania;
use App\Util\LogErrorManager;
use App\Util\ResultManager;
use App\Util\RuleManager;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EncuestaController extends BaseController
{
    public function __construct()
    {
        // 'store' y 'showViewEncuesta' quedan publicos para el flujo de encuesta por QR.
        parent::__construct(['store', 'showViewEncuesta']);
    }

    /**
     * Muestra la vista publica de encuesta.
     *
     * @return \Illuminate\Http\Response
     */
    public function showViewEncuesta()
    {
        $pagina = PaginaDinamica::select('titulo', 'slug', 'descripcion', 'idcampania', 'api_token')
            ->where([
                ['slug', 'encuesta'],
                ['estado_general', 'A']
            ])
            ->first();

        return view('encuesta', compact('pagina'));
    }

    /**
     * Obtiene la campana fija de la encuesta publica.
     * Si no existe configuracion en pagina dinamica, retorna null.
     *
     * @return int|null
     */
    private function getCampaniaFijaEncuestaId()
    {
        $pagina = PaginaDinamica::select('idcampania')
            ->where('slug', 'encuesta')
            ->where('estado_general', 'A')
            ->first();

        return $pagina ? (int) $pagina->idcampania : null;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = $this->getFilters($request, new Encuesta());
        $perpage = $this->getLimitPagination($request);

        $queryencuesta = Encuesta::where($filters)
            ->with('campania', 'evento');

        if ($this->user->idcuenta != RuleManager::SYSTEM_COMPANY_ACCOUNT || !in_array($this->user->idrol, RuleManager::ADMINISTRATORS_ACCESS)) {
            // filtro mostrar solo encuestas que pertenecen a la cuenta del usuario
            $queryencuesta->whereHas('campania', function ($query) {
                $query->where('idcuenta', '=', $this->user->idcuenta);
            });
        }

        if ($request->exists('idcampania')) {
            $queryencuesta->where('idcampania', $request->idcampania);
        }

        if ($request->exists('idevento')) {
            $queryencuesta->where('idevento', $request->idevento);
        }

        if ($request->exists('fechadesde')) {
            $fechadesde = Carbon::createFromFormat('d-m-Y', $request->fechadesde)->toDateString();
            $queryencuesta->whereDate('dateinsert', '>=', $fechadesde);
        }

        if ($request->exists('fechahasta')) {
            $fechahasta = Carbon::createFromFormat('d-m-Y', $request->fechahasta)->toDateString();
            $queryencuesta->whereDate('dateinsert', '<=', $fechahasta);
        }

        $encuesta = $queryencuesta->orderBy('idencuesta', 'DESC')
            ->paginate($perpage);

        return $encuesta;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return Encuesta::with('campania', 'evento')->findOrFail($id);
    }

    /**
     * Store a newly created resource in storage.
     * Endpoint público: lo llena el asistente desde el formulario de encuesta.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = "";

        $idCampaniaFija = $this->getCampaniaFijaEncuestaId();
        if (!$idCampaniaFija) {
            return ResultManager::warningMessage('No hay campana activa configurada para la encuesta.');
        }

        // Se fuerza la campana fija para evitar depender de datos del cliente.
        $request->merge(['idcampania' => $idCampaniaFija]);

        $validator = Validator::make($request->all(), [
            'idcampania' => 'required|integer',
            'idevento' => 'nullable|integer',
            'q1_conocimiento_antes' => 'required|integer|between:1,4',
            'q2_conocimiento_despues' => 'required|integer|between:1,4',
            'q3_gusto_mas' => 'required|string|max:500',
            'q4_sentimiento_general' => 'required|integer|between:1,5',
            'q5_taller_general' => 'nullable|integer|between:1,5',
            'q5_tallerista' => 'nullable|integer|between:1,5',
            'q5_aprendio_nuevo' => 'nullable|integer|between:1,5',
            'q6_nps' => 'required|integer|between:0,10',
            'q7_intencion_estudiar' => 'required|integer|between:1,5',
            'q8_que_cambiarias' => ['required', 'string', 'max:2000', 'regex:/.*\\S.*/'],
            'q9_como_se_entero' => 'required|integer|between:1,6',
            'q10_situacion_actual' => 'required|integer|between:1,6',
            'origen_qr' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return ResultManager::warningMessage('Por favor completa todas las preguntas obligatorias de la encuesta.');
        }

        $q5 = [
            $request->q5_taller_general,
            $request->q5_tallerista,
            $request->q5_aprendio_nuevo,
        ];

        $q5Completas = collect($q5)->filter(function ($v) {
            return !is_null($v) && $v !== '';
        })->count();

        if ($q5Completas > 0 && $q5Completas < 3) {
            return ResultManager::warningMessage('Si participaste en taller, completa las 3 calificaciones del bloque de talleres.');
        }

        try {
            $encuesta = new Encuesta();
            $encuesta = $this->setModel($encuesta, $request);
            $encuesta->ip_hash = hash('sha256', $request->ip());
            $encuesta->user_agent_hash = hash('sha256', (string) $request->userAgent());
            $encuesta->userinsert = $this->user ? $this->user->email : 'encuesta_publica';
            $encuesta->save();

            $result = ResultManager::successMessage('¡Gracias por completar la encuesta!');

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::errorMessage('Intente nuevamente más tarde.');

        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::gerericErrorMessage();
        }

        return $result;
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
        $result = "";

        try {
            $encuesta = $this->setModel(Encuesta::findOrFail($id), $request);
            $encuesta->userupdate = $this->user->email;
            $encuesta->update();

            $result = ResultManager::genericSuccessMessage();

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::errorMessage('No se pudo actualizar el registro.');

        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::gerericErrorMessage();
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage (soft delete via estado).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = "";

        try {
            $encuesta = Encuesta::findOrFail($id);
            $encuesta->estado = $encuesta->estado == RuleManager::DISABLED_STATE ? RuleManager::ACTIVE_STATE : RuleManager::DISABLED_STATE;
            $encuesta->userupdate = $this->user->email;
            $encuesta->update();

            if ($encuesta->estado == RuleManager::ACTIVE_STATE) {
                $result = ResultManager::successMessage('Encuesta restaurada correctamente.');
            } else if ($encuesta->estado == RuleManager::DISABLED_STATE) {
                $result = ResultManager::warningMessage('Encuesta eliminada correctamente.');
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
     * Remove the specified resource from storage permanently.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function physicallyRemove(Request $request)
    {
        $result = "";

        $id = $request->id;
        try {
            Encuesta::destroy($id);
            $result = ResultManager::successMessage('Registro eliminado correctamente.');

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
     * Devuelve indicadores agregados de la encuesta (NPS, promedios, distribución q3).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function estadisticas(Request $request)
    {
        $idCampaniaFija = $this->getCampaniaFijaEncuestaId();
        if (!$idCampaniaFija) {
            return response()->json([
                'total' => 0,
                'nps' => 0,
                'promedio_sentimiento' => 0,
                'promedio_conocimiento_despues' => 0,
                'gusto_mas' => [],
            ]);
        }

        $filters = $this->getFilters($request, new Encuesta());

        $query = Encuesta::where($filters)
            ->where('estado', RuleManager::ACTIVE_STATE)
            ->where('idcampania', $idCampaniaFija);

        if ($request->exists('idevento')) {
            $query->where('idevento', $request->idevento);
        }

        $total = (clone $query)->count();

        if ($total === 0) {
            return response()->json([
                'total' => 0,
                'nps' => 0,
                'promedio_sentimiento' => 0,
                'promedio_conocimiento_despues' => 0,
                'gusto_mas' => [],
            ]);
        }

        $promotores = (clone $query)->where('q6_nps', '>=', 9)->count();
        $detractores = (clone $query)->where('q6_nps', '<=', 6)->count();
        $nps = round((($promotores - $detractores) / $total) * 100, 1);

        $promedioSentimiento = round((clone $query)->avg('q4_sentimiento_general'), 2);
        $promedioConocimiento = round((clone $query)->avg('q2_conocimiento_despues'), 2);

        // distribución de q3_gusto_mas (campo con códigos separados por coma)
        $todos = (clone $query)->pluck('q3_gusto_mas');
        $conteoGustos = [];
        foreach ($todos as $codigos) {
            foreach (explode(',', $codigos) as $codigo) {
                $codigo = trim($codigo);
                if ($codigo === '') {
                    continue;
                }
                $conteoGustos[$codigo] = ($conteoGustos[$codigo] ?? 0) + 1;
            }
        }
        arsort($conteoGustos);

        return response()->json([
            'total' => $total,
            'nps' => $nps,
            'promedio_sentimiento' => $promedioSentimiento,
            'promedio_conocimiento_despues' => $promedioConocimiento,
            'gusto_mas' => $conteoGustos,
        ]);
    }

    /**
     * Reporte completo por preguntas para encuesta anonima.
     * Devuelve total de respuestas y distribucion de cada pregunta.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportePreguntas(Request $request)
    {
        $idCampaniaFija = $this->getCampaniaFijaEncuestaId();
        if (!$idCampaniaFija) {
            return response()->json([
                'resumen' => [
                    'total_encuestas' => 0,
                    'respondieron_taller' => 0,
                    'no_respondieron_taller' => 0,
                ],
                'preguntas' => [
                    'q1_conocimiento_antes' => [],
                    'q2_conocimiento_despues' => [],
                    'q3_gusto_mas' => [],
                    'q4_sentimiento_general' => [],
                    'q5_taller_general' => [],
                    'q5_tallerista' => [],
                    'q5_aprendio_nuevo' => [],
                    'q6_nps_distribucion' => [],
                    'q7_intencion_estudiar' => [],
                    'q9_como_se_entero' => [],
                    'q10_situacion_actual' => [],
                ],
                'nps' => [
                    'promotores' => 0,
                    'pasivos' => 0,
                    'detractores' => 0,
                    'score' => 0,
                ],
                'comentarios_q8' => [],
                'campania_fija' => null,
            ]);
        }

        $query = Encuesta::where('estado', RuleManager::ACTIVE_STATE)
            ->where('idcampania', $idCampaniaFija);

        if ($this->user->idcuenta != RuleManager::SYSTEM_COMPANY_ACCOUNT || !in_array($this->user->idrol, RuleManager::ADMINISTRATORS_ACCESS)) {
            $query->whereHas('campania', function ($q) {
                $q->where('idcuenta', '=', $this->user->idcuenta);
            });
        }

        if ($request->exists('idevento')) {
            $query->where('idevento', $request->idevento);
        }

        if ($request->exists('fechadesde')) {
            $fechadesde = Carbon::createFromFormat('d-m-Y', $request->fechadesde)->toDateString();
            $query->whereDate('dateinsert', '>=', $fechadesde);
        }

        if ($request->exists('fechahasta')) {
            $fechahasta = Carbon::createFromFormat('d-m-Y', $request->fechahasta)->toDateString();
            $query->whereDate('dateinsert', '<=', $fechahasta);
        }

        $totalEncuestas = (clone $query)->count();

        $distribucion = function ($column) use ($query) {
            return (clone $query)
                ->selectRaw("{$column} as opcion, COUNT(*) as total")
                ->whereNotNull($column)
                ->groupBy($column)
                ->orderBy($column)
                ->get()
                ->map(function ($row) {
                    return [
                        'opcion' => (int) $row->opcion,
                        'total' => (int) $row->total,
                    ];
                })
                ->values();
        };

        $gustoMas = [];
        $q3Registros = (clone $query)->pluck('q3_gusto_mas');
        foreach ($q3Registros as $registro) {
            foreach (explode(',', (string) $registro) as $item) {
                $codigo = trim($item);
                if ($codigo === '') {
                    continue;
                }
                $gustoMas[$codigo] = ($gustoMas[$codigo] ?? 0) + 1;
            }
        }
        arsort($gustoMas);

        $nps = [];
        for ($i = 0; $i <= 10; $i++) {
            $nps[$i] = 0;
        }
        $npsRows = (clone $query)
            ->selectRaw('q6_nps as opcion, COUNT(*) as total')
            ->groupBy('q6_nps')
            ->orderBy('q6_nps')
            ->get();
        foreach ($npsRows as $row) {
            $nps[(int) $row->opcion] = (int) $row->total;
        }

        $promotores = (clone $query)->where('q6_nps', '>=', 9)->count();
        $pasivos = (clone $query)->whereBetween('q6_nps', [7, 8])->count();
        $detractores = (clone $query)->where('q6_nps', '<=', 6)->count();
        $npsScore = $totalEncuestas > 0 ? round((($promotores - $detractores) / $totalEncuestas) * 100, 1) : 0;

        $comentariosQ8 = (clone $query)
            ->select('idencuesta', 'q8_que_cambiarias', 'dateinsert')
            ->whereNotNull('q8_que_cambiarias')
            ->where('q8_que_cambiarias', '!=', '')
            ->orderBy('idencuesta', 'DESC')
            ->limit(100)
            ->get();

        return response()->json([
            'resumen' => [
                'total_encuestas' => $totalEncuestas,
                'respondieron_taller' => (clone $query)->whereNotNull('q5_taller_general')->count(),
                'no_respondieron_taller' => (clone $query)->whereNull('q5_taller_general')->count(),
            ],
            'preguntas' => [
                'q1_conocimiento_antes' => $distribucion('q1_conocimiento_antes'),
                'q2_conocimiento_despues' => $distribucion('q2_conocimiento_despues'),
                'q3_gusto_mas' => $gustoMas,
                'q4_sentimiento_general' => $distribucion('q4_sentimiento_general'),
                'q5_taller_general' => $distribucion('q5_taller_general'),
                'q5_tallerista' => $distribucion('q5_tallerista'),
                'q5_aprendio_nuevo' => $distribucion('q5_aprendio_nuevo'),
                'q6_nps_distribucion' => $nps,
                'q7_intencion_estudiar' => $distribucion('q7_intencion_estudiar'),
                'q9_como_se_entero' => $distribucion('q9_como_se_entero'),
                'q10_situacion_actual' => $distribucion('q10_situacion_actual'),
            ],
            'nps' => [
                'promotores' => $promotores,
                'pasivos' => $pasivos,
                'detractores' => $detractores,
                'score' => $npsScore,
            ],
            'comentarios_q8' => $comentariosQ8,
            'campania_fija' => Campania::select('idcampania', 'nombrecampania')->find($idCampaniaFija),
        ]);
    }

    private function setModel(Encuesta $encuesta, Request $request): Encuesta
    {
        $encuesta->idcampania = $request->idcampania;
        $encuesta->idevento = $request->idevento;
        $encuesta->q1_conocimiento_antes = $request->q1_conocimiento_antes;
        $encuesta->q2_conocimiento_despues = $request->q2_conocimiento_despues;
        $encuesta->q3_gusto_mas = $request->q3_gusto_mas;
        $encuesta->q4_sentimiento_general = $request->q4_sentimiento_general;
        $encuesta->q5_taller_general = $request->q5_taller_general;
        $encuesta->q5_tallerista = $request->q5_tallerista;
        $encuesta->q5_aprendio_nuevo = $request->q5_aprendio_nuevo;
        $encuesta->q6_nps = $request->q6_nps;
        $encuesta->q7_intencion_estudiar = $request->q7_intencion_estudiar;
        $encuesta->q8_que_cambiarias = trim((string)$request->q8_que_cambiarias);
        $encuesta->q9_como_se_entero = $request->q9_como_se_entero;
        $encuesta->q10_situacion_actual = $request->q10_situacion_actual;
        $encuesta->origen_qr = $request->origen_qr;

        return $encuesta;
    }
}
