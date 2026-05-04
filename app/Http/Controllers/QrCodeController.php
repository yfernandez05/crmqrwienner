<?php

namespace App\Http\Controllers;

use App\Mail\MessageReceived;
use App\Models\Cliente;
use App\Models\Evento;
use App\Models\EventoCliente;
use App\Util\LogErrorManager;
use App\Util\ResultManager;
use App\Util\RuleManager;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{

    public function decryptQRCode(Request $qrCodeData)
    {

        $datosDescifrados = $this->decryptBase64($qrCodeData->qrCode);

        $datos = json_decode($datosDescifrados, true);

        if(!$datos){
            return ResultManager::warningMessage('QR no valido');
        }

        return $this->authLogin($datos);
    }

    private function decryptBase64($data)
    {
        $decodedData = base64_decode($data);
        return $decodedData;
    }

    public function authLogin($dataQR)
    {
        $result = '';

        try {
            if(Auth()->user()->idrol == RuleManager::PERSONAL_ATTENDANCE_ACCESS){
                $result = $this->readingAssistanceQR($dataQR);
            }

            if(Auth()->user()->idrol == RuleManager::PERSONAL_STAND_ACCESS){
                $result = $this->readingStandQR($dataQR);
            }

            if(empty($result)){
                $result = ResultManager::errorMessage("No se pudo leer el QR. Intentelo nuevamente.");
            }
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::errorMessage("Ocurrió un error inesperado. Intente nuevamente.");
        }

        return $result;
    }

    public function readingAssistanceQR($dataQR)
    {
        $result = '';
        $errorMsg = 'Error al leer el QR, intentelo nuevamente';

        try {

            $clientAsisten = Cliente::where(['email' => $dataQR['correo'], 'idcampania' => $dataQR['idcampania'], 'estado' => RuleManager::ACTIVE_STATE])->first();

            if (!$clientAsisten) {
                return ResultManager::warningMessage(' No se encontró ningún registro válido con este QR.');
            }

            // Validar campaña solo si el usuario tiene idcampania asignado
            // if (Auth::user()->idcampania && Auth::user()->idcampania !== $clientAsisten->idcampania) {
            //     return ResultManager::warningMessage('Este QR no corresponde a este evento. Asegúrate de usar el QR que te fue asignado recientemente.');
            // }

            if (Auth::user()->idcampania !== $clientAsisten->idcampania) {
                return ResultManager::warningMessage('Este QR no corresponde a este evento. Asegúrate de usar el QR que te fue asignado recientemente.');
            }

            $grado = $clientAsisten->grado ?? '';
            // isbeca18 eliminado

            if($clientAsisten->asistencia){

                if (empty($grado)) {
                    return ResultManager::warningMessage('<p class="text-center">EL QR YA FUE USADO<p>');
                } else {

                    $dataQR['config'] = RuleManager::getQRConfigColor($grado);
                    $config = $dataQR['config'];

                    $textColor = $config['color'] === 'white' ? 'black' : 'white';
                    $borderColor = $config['color'] === 'white' ? 'gray' : $config['color'];

                    return ResultManager::warningMessage('<p class="text-center">EL QR YA FUE USADO <p>
                        <h4 style="
                            text-transform: uppercase;
                            background-color: ' . $config['color'] . ';
                            padding: 10px;
                            margin-top: 1em;
                            color: ' . $textColor . ';
                            border: 2px solid ' . $borderColor . ';
                            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);">
                            ' . $config['description'] . '
                        </h4>'
                    );
                }
            };

            $clientAsisten->asistencia = 1;

            $anioAsistencia = (int)optional($clientAsisten->fechaasistencia)->format('Y');

            if ($anioAsistencia < 1970) {
                $clientAsisten->fechaasistencia = Carbon::now()->format('y-m-d H:i:s');
            }

            $dataQR['config'] = RuleManager::getQRConfigColor($grado);
            $dataQR['ismatricula'] = (int) $clientAsisten->ismatricula;
            $dataQR['idcliente'] = $clientAsisten->idcliente; // Asegura que el idcliente esté presente


            $clientAsisten->update();

            $result = ResultManager::successMessageData('El QR si es valido', $dataQR);

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::errorMessage($errorMsg);
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::warningMessage($errorMsg);
        }

        return $result;
    }

    public function readingStandQR($dataQR)
    {
        $result = '';
        $errorMsg = 'Error al leer el QR, intentelo nuevamente';
        $idevento = Auth::user()->idevento;

        try {
            DB::beginTransaction();

            $clientAsisten = Cliente::where([
                'email' => $dataQR['correo'],
                'idcampania' => $dataQR['idcampania'],
                'estado' => RuleManager::ACTIVE_STATE
            ])->first();


            if (!$clientAsisten) {
                return ResultManager::warningMessage('No se encontró ningún registro válido con este QR.');
            }

            $evento = Evento::find($idevento);

            if (!$evento) {
                return ResultManager::warningMessage('Evento no encontrado.');
            }

            $existsClientEvent = EventoCliente::where('idcliente', $clientAsisten->idcliente)
                           ->where('idevento', $idevento)
                           ->exists();



            if ($existsClientEvent) {
               return ResultManager::warningMessage('EL PARTICIPANTE YA SE ENCUENTRA REGISTRADO');
            }


            if ($clientAsisten->asistencia < 1) {
                return ResultManager::warningMessage('<p class="text-center">EL ASISTENTE <b class="font-weight-bold">'.$dataQR['nombre'].'</b> NO MARCO SU ASISTENCIA EN LA ENTRADA.</p>');
            }



            EventoCliente::create([
                'idevento' => $idevento,
                'idcliente' => $clientAsisten['idcliente'],
                'nombres' => $clientAsisten['nombres'],
                'apellidopaterno' => $clientAsisten['apellidopaterno'],
                'apellidomaterno' => $clientAsisten['apellidomaterno'],
                'email' => $clientAsisten['email'],
                'dni' => $clientAsisten['dni'],
                'telefono' => $clientAsisten['telefono'],
                'prefijo' => $clientAsisten['prefijo'],
                'fecharegistro' => $clientAsisten['fecharegistro'],
                'coddistrito' => $clientAsisten['coddistrito'],
                'utm' => $clientAsisten['utm'],
                'campaign_content' => $clientAsisten['campaign_content'],
                'campaign_medium' => $clientAsisten['campaign_medium'],
                'campaign_name' => $clientAsisten['campaign_name'],
                'campaign_source' => $clientAsisten['campaign_source'],
                'campaign_term' => $clientAsisten['campaign_term'],
                'procedencia' => $clientAsisten['procedencia'],
                'grado' => $clientAsisten['grado'],
                'carrera' => $clientAsisten['carrera'],
                'colegio' => $clientAsisten['colegio'],
                'sede_destino' => $clientAsisten['sede_destino'],
                'uuidimportacion' => $clientAsisten['uuidimportacion'],
                'asistencia' => $clientAsisten['asistencia'],
                'fechaasistencia' => Carbon::now()->format('y-m-d H:i:s'),
                'estado' => $clientAsisten['estado'],
                'tipodocumento' => $clientAsisten['tipodocumento'],
                'ismatricula' => $clientAsisten['ismatricula'],
                'uuidmatriculaimportacion' => $clientAsisten['uuidmatriculaimportacion'],
                'anioegreso' => $clientAsisten['anioegreso'],
            ]);



            DB::commit();

            $dataQR['isStand'] = true;
            $result = ResultManager::successMessageData('PARTICIPANTE REGISTRADO', $dataQR);

        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                $result = ResultManager::errorMessage('El participante ya está registrado en este stand.');
            } else {
                LogErrorManager::saveInDB($this, __FUNCTION__, $e);
                $result = ResultManager::errorMessage('Error SQL: ' . $e->getMessage());
            }
            DB::rollBack();
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::warningMessage('Error general: ' . $e->getMessage());
            DB::rollBack();
        }

        return $result;
    }


    public function markAttendanceEntry(Request $request)
    {

        $result = '';
        $errorMsg = 'No se registro la asistencia, intente realizar la busqueda nuevamente.';

        try {

            $clientAsisten = Cliente::where(['idcliente' => $request->idcliente, 'idcampania' => $request->idcampania])->first();

            $clientAsisten->asistencia = 1;

            $anioAsistencia = (int)optional($clientAsisten->fechaasistencia)->format('Y');

            if ($anioAsistencia < 1970) {
                $clientAsisten->fechaasistencia = Carbon::now()->format('y-m-d H:i:s');
            }

            $clientAsisten->update();

            $result = ResultManager::successMessageData('Asistencia Registrada Exitosamente.', $clientAsisten);

        } catch (QueryException $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::errorMessage($errorMsg);
        } catch (Exception $e) {
            LogErrorManager::saveInDB($this, __FUNCTION__, $e);
            $result = ResultManager::errorMessage($errorMsg);
        }

        return $result;
    }


}
