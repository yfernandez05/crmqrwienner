<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionQrColor extends Model
{
    protected $table = 'configuracion_qr_colores';
    protected $primaryKey = 'idconfig';

    public $timestamps = false;

    protected $fillable = [
        'anio_egreso',
        'es_beca18',
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
        'es_beca18' => 'integer',
        'orden' => 'integer'
    ];

    /**
     * Obtener la configuración de color según año de egreso y beca18
     */
    public static function obtenerConfiguracion($anioEgreso, $isBeca18)
    {
        // Buscar coincidencia exacta primero (año + beca18)
        $config = self::where('estado', 'A')
            ->where('anio_egreso', $anioEgreso)
            ->where('es_beca18', $isBeca18)
            ->orderBy('orden')
            ->first();

        if ($config) {
            return self::formatearConfiguracion($config);
        }

        // Buscar por año sin importar beca18
        $config = self::where('estado', 'A')
            ->where('anio_egreso', $anioEgreso)
            ->orderBy('orden')
            ->first();

        if ($config) {
            return self::formatearConfiguracion($config);
        }

        // Si es Beca18, buscar configuración de egresados Beca18
        if ($isBeca18 == 1 && $anioEgreso <= 2024) {
            $config = self::where('estado', 'A')
                ->whereNull('anio_egreso')
                ->where('es_beca18', 1)
                ->orderBy('orden')
                ->first();

            if ($config) {
                return self::formatearConfiguracion($config);
            }
        }

        // Configuración por defecto (egresados generales)
        $config = self::where('estado', 'A')
            ->whereNull('anio_egreso')
            ->where('es_beca18', 0)
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
