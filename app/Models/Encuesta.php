<?php

namespace App\Models;

use App\Models\Campania;
use App\Models\Evento;
use App\Util\RuleManager;
use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    protected $table = 'encuesta_wiener_fest';
    protected $primaryKey = 'idencuesta';
    public $timestamps = false;

    protected $fillable = [
        'idcampania',
        'idevento',
        'q1_conocimiento_antes',
        'q2_conocimiento_despues',
        'q3_gusto_mas',
        'q4_sentimiento_general',
        'q5_taller_general',
        'q5_tallerista',
        'q5_aprendio_nuevo',
        'q6_nps',
        'q7_intencion_estudiar',
        'q8_que_cambiarias',
        'q9_como_se_entero',
        'q10_situacion_actual',
        'estado',
        'ip_hash',
        'user_agent_hash',
        'origen_qr',
    ];

    protected $hidden = [
        'userinsert',
        'dateinsert',
        'userupdate',
        'dateupdate',
        'ip_hash',
        'user_agent_hash',
    ];

    protected $casts = [
        'dateinsert' => 'datetime:Y-m-d H:i:s',
        'dateupdate' => 'datetime:Y-m-d H:i:s',
    ];

    protected $appends = [
        'isactive',
        'fecha',
        'q1_conocimiento_antes_name',
        'q2_conocimiento_despues_name',
        'q3_gusto_mas_array',
        'q4_sentimiento_general_name',
        'q6_nps_categoria',
        'q7_intencion_estudiar_name',
        'q9_como_se_entero_name',
        'q10_situacion_actual_name',
        'asistio_taller',
    ];

    public function getIsactiveAttribute()
    {
        return RuleManager::getIsActive($this->estado);
    }

    public function getFechaAttribute()
    {
        return optional($this->dateinsert)->format('d-m-Y H:i');
    }

    public function getQ1ConocimientoAntesNameAttribute()
    {
        $map = [
            1 => 'No la conocia para nada',
            2 => 'La habia escuchado pero no sabia mucho',
            3 => 'La conocia mas o menos',
            4 => 'La conocia muy bien, ya la estaba considerando',
        ];

        return $map[$this->q1_conocimiento_antes] ?? '';
    }

    public function getQ2ConocimientoDespuesNameAttribute()
    {
        $map = [
            1 => 'Igual que antes',
            2 => 'La conozco un poco mas',
            3 => 'La conozco bastante mejor',
            4 => 'Ahora la conozco muy bien',
        ];

        return $map[$this->q2_conocimiento_despues] ?? '';
    }

    public function getQ3GustoMasArrayAttribute()
    {
        if (empty($this->q3_gusto_mas)) {
            return [];
        }

        return array_map('trim', explode(',', $this->q3_gusto_mas));
    }

    public function getQ4SentimientoGeneralNameAttribute()
    {
        $map = [
            1 => 'Mala',
            2 => 'Regular',
            3 => 'Bien',
            4 => 'Muy bien',
            5 => 'Genial',
        ];

        return $map[$this->q4_sentimiento_general] ?? '';
    }

    public function getAsistioTallerAttribute()
    {
        return !is_null($this->q5_taller_general);
    }

    public function getQ6NpsCategoriaAttribute()
    {
        if (is_null($this->q6_nps)) {
            return '';
        }

        if ($this->q6_nps >= 9) {
            return 'Promotor';
        }

        if ($this->q6_nps >= 7) {
            return 'Pasivo';
        }

        return 'Detractor';
    }

    public function getQ7IntencionEstudiarNameAttribute()
    {
        $map = [
            1 => 'Si, ya tome la decision',
            2 => 'Si, es una de mis opciones',
            3 => 'Lo estoy evaluando',
            4 => 'Por ahora no',
            5 => 'Ya soy alumno/a o egresado/a',
        ];

        return $map[$this->q7_intencion_estudiar] ?? '';
    }

    public function getQ9ComoSeEnteroNameAttribute()
    {
        $map = [
            1 => 'Redes sociales',
            2 => 'WhatsApp o mensaje',
            3 => 'Amigo o familiar',
            4 => 'Colegio o instituto',
            5 => 'Email',
            6 => 'Me avisaron en la U',
        ];

        return $map[$this->q9_como_se_entero] ?? '';
    }

    public function getQ10SituacionActualNameAttribute()
    {
        $map = [
            1 => 'En el colegio (4to o 5to)',
            2 => 'Egrese, aun no estudio',
            3 => 'Estudio en otra universidad',
            4 => 'Trabajo o soy profesional',
            5 => 'Soy alumno/a de Wiener',
            6 => 'Soy padre o madre',
        ];

        return $map[$this->q10_situacion_actual] ?? '';
    }

    public function campania()
    {
        return $this->belongsTo(Campania::class, 'idcampania', 'idcampania');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'idevento', 'idevento');
    }
}
