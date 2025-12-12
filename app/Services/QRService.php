<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Mail;
use App\Mail\MessageReceived;
use App\Models\Cliente;
use Illuminate\Support\Facades\Log;
use Throwable;

class QRService
{
    protected $imageManager;

    public function __construct()
    {
        // Configurar el driver GD (no requiere imagick)
        $this->imageManager = new ImageManager(['driver' => 'gd']);
    }

    /**
     * Genera QR y opcionalmente envía correo
     */
    public function generateQRAndSendmail(array $datos, bool $enviarCorreo = false, Cliente $cliente = null)
    {
        try {
            $datosJSON = json_encode($datos);
            $datosCifrados = base64_encode($datosJSON);

            // Generar el QR en PNG y guardarlo temporalmente
            $tempPath = storage_path('app/temp_qr_' . uniqid() . '.png');
            QrCode::format('png')->size(400)->generate($datosCifrados, $tempPath);

            // Añadir padding y logo
            $qrFinal = $this->paddinQRpng($tempPath, 50);

            // Eliminar archivo temporal
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            // Enviar correo si se solicita
            if ($enviarCorreo && isset($datos['correo']) && $cliente) {
                Mail::to($datos['correo'])->send(new MessageReceived($cliente, $qrFinal));
            }

            return $qrFinal;
        } catch (Throwable $e) {
            Log::error('Error en QRService->generateQRAndSendmail: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Añade padding y logo al QR
     */
    public function paddinQRpng($imgPath, $padding)
    {
        // Crear imagen desde archivo usando el driver GD
        $qrImage = $this->imageManager->make($imgPath);

        $canvasWidth = $qrImage->width() + 2 * $padding;
        $canvasHeight = $qrImage->height() + 2 * $padding;

        // Crear canvas con fondo blanco
        $canvas = $this->imageManager->canvas($canvasWidth, $canvasHeight, '#ffffff');

        // Insertar QR en el centro
        $canvas->insert($qrImage, 'center');

        // Insertar logo si existe
        $logoPath = public_path('images/logo-qr-2.png');
        if (file_exists($logoPath)) {
            $logo = $this->imageManager->make($logoPath)->resize(65, 65);
            $canvas->insert($logo, 'center');
        }

        // Retornar como PNG
        return $canvas->encode('png', 90)->__toString();
    }
}
