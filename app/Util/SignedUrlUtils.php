<?php

namespace App\Util;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SignedUrlUtils
{
    /**
     * Genera una URL firmada basada en una ruta existente.
     *
     * @param string $routeName Nombre de la ruta (ejemplo: 'cliente.descargatuentrada')
     * @param array $params Parámetros de la ruta
     * @return string URL firmada completa
    */

    public static function generateSignedUrl(string $routeName, array $params = []): string
    {
        // Generar la ruta sin dominio (solo path)
        $path = route($routeName, $params, false);

        // Obtener clave de aplicación
        $key = env('APP_KEY');
        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(Str::after($key, 'base64:'));
        }

        // Crear firma con HMAC SHA256
        $signature = hash_hmac('sha256', $path, $key);

        // Retornar la URL completa con la firma
        return url($path) . '?signature=' . $signature;
    }
}
