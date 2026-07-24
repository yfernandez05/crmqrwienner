<?php

namespace App\Util;

use Carbon\Carbon;

class SedeHelper
{
    /**
     * Devuelve informacion de la sede (direccion, evento y fecha formateada)
     */
    public static function getSedeInfo($sede, $campania): array
    {
        $nombrevento = 'GRADUACION CIENTIFICA 2025';

        $fechainicio = null;
        $fechafin = null;

        if (is_array($campania)) {
            $fechainicio = $campania['fechainicio'] ?? null;
            $fechafin = $campania['fechafin'] ?? null;
        } elseif (is_object($campania)) {
            $fechainicio = $campania->fechainicio ?? null;
            $fechafin = $campania->fechafin ?? null;
        }

        $fechaEvento = self::formatearFechaEvento($fechainicio, $fechafin);

        $sedes = [
            'Campus Villa' => [
                'direccion' => 'Km. 19 de Panamericana Sur - Villa - Campus Villa 2',
                'fechaevento' => $fechaEvento,
                'nombrevento' => $nombrevento,
                'urlmaps' => 'https://maps.app.goo.gl/czb6vYFZSNF4Cz5X6',
            ],
            'Campus Ate' => [
                'direccion' => 'Av. Nicolas Ayllon 7208',
                'fechaevento' => $fechaEvento,
                'nombrevento' => $nombrevento,
                'urlmaps' => 'https://maps.app.goo.gl/AKrMk3Nxsi5KLbYd9',
            ],
            'Campus Norte' => [
                'direccion' => 'Av. Alfredo Mendiola con Av. 2 de octubre (Ovalo Infantas), Los Olivos',
                'fechaevento' => $fechaEvento,
                'nombrevento' => $nombrevento,
                'urlmaps' => 'https://maps.app.goo.gl/8nc7huF9WNmudWF28',
            ],
            'Campus Aramburu' => [
                'direccion' => 'Av. Republica de Panama 3944, Surquillo',
                'fechaevento' => $fechaEvento,
                'nombrevento' => $nombrevento,
                'urlmaps' => 'https://maps.app.goo.gl/JEztrrU1C1HU7NRd7',
            ],
        ];

        return $sedes[$sede] ?? [
            'direccion' => 'Direccion no disponible',
            'fechaevento' => 'Fecha por confirmar',
            'nombrevento' => $nombrevento,
            'urlmaps' => '#',
        ];
    }

    /**
     * Formatea un rango de fechas en formato legible en espanol.
     * Ejemplo: del 25 al 27 de Abril del 2025
     */
    public static function formatearFechaEvento($fechainicio, $fechafin): string
    {
        if (!$fechainicio || !$fechafin) {
            return 'Fecha por confirmar';
        }

        Carbon::setLocale('es');

        $inicio = Carbon::parse($fechainicio);
        $fin = Carbon::parse($fechafin);

        $mismoDia = $inicio->isSameDay($fin);
        $mismoMes = $inicio->month === $fin->month;
        $mismoAnio = $inicio->year === $fin->year;

        $mesInicio = ucfirst($inicio->translatedFormat('F'));
        $mesFin = ucfirst($fin->translatedFormat('F'));

        if ($mismoDia && $mismoMes && $mismoAnio) {
            return $inicio->day . ' de ' . $mesInicio . ' del ' . $inicio->year;
        }

        if ($mismoMes && $mismoAnio) {
            return 'del ' . $inicio->day . ' al ' . $fin->day . ' de ' . $mesInicio . ' del ' . $inicio->year;
        }

        if ($mismoAnio) {
            return 'del ' . $inicio->day . ' de ' . $mesInicio . ' al ' . $fin->day . ' de ' . $mesFin . ' del ' . $inicio->year;
        }

        return 'del ' . $inicio->day . ' de ' . $mesInicio . ' del ' . $inicio->year . ' al ' . $fin->day . ' de ' . $mesFin . ' del ' . $fin->year;
    }
}
