<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionQrColor extends Model
{
    protected $table = 'configuracion_qr_colores';
    protected $primaryKey = 'idconfig';

    public $timestamps = false;

    protected $fillable = [
        'grado',
        'color',
        'descripcion',
        'orden',
        'estado',
        'userinsert',
        'dateinsert',
        'userupdate',
        'dateupdate'
    ];

    protected $casts = [
        'orden' => 'integer'
    ];

    /**
     * Obtener la configuración de color según año de egreso y beca18
     */
    public static function obtenerConfiguracion($grado)
    {
        // Buscar coincidencia exacta por grado
        $config = self::where('estado', 'A')
            ->where('grado', $grado)
            ->orderBy('orden')
            ->first();

        if ($config) {
            return self::formatearConfiguracion($config);
        }

        // Configuración por defecto (egresados generales)
        $config = self::where('estado', 'A')
            ->whereNull('grado')
            ->orderBy('orden')
            ->first();

        return $config ? self::formatearConfiguracion($config) : self::getDefaultConfig();
    }

    /**
     * Formatear la configuración para el formato esperado
     */
    private static function formatearConfiguracion($config)
    {
        $textColor = $config->color === 'white' ? 'black' : 'white';
        $borderColor = $config->color === 'white' ? 'gray' : $config->color;

        return [
            'color' => $config->color,
            'description' => $config->descripcion,
            'border' => '2px solid ' . $borderColor,
            'textColor' => $textColor
        ];
    }

    /**
     * Configuración por defecto si no se encuentra ninguna
     */
    private static function getDefaultConfig()
    {
        return [
            'color' => 'white',
            'description' => 'Egresado',
            'border' => '2px solid gray',
            'textColor' => 'black'
        ];
    }
}
