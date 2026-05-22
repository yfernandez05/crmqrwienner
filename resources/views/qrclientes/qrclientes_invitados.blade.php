<!DOCTYPE html>
<html lang="es">

<head>
  <title>Entrada a la Feria de Carreras</title>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

  <style>
    @page {
      margin: 10mm;
      /* Define márgenes de 10mm en todos los lados */
    }

    body {
      margin: 0;
      background-color: #f2f2f2;
      color: black;
    }

    p {
      margin: 0;
      padding: 0;
    }

    table,
    tr,
    td,
    th {
      border: none;
      border-collapse: collapse;
      /* Asegura que no haya separación */
    }

    .body-bg {
      margin: 0;
      font-family: "Segoe UI Emoji", "Noto Color Emoji", Arial, sans-serif;
      padding: 20px;
      font-size: 14px;
    }

    /* PARTICIPANTE */
    .ticket-participante {
      max-width: 100%;
      margin: auto;
      background: white;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      /* border-left: 10px solid #1D428A; */
    }

    .ticket-info,
    .ticket-qr {
      padding: 30px;
      /* width: 50%; */
      vertical-align: middle;
    }

    .ticket-info {
      background-color: #fff;
      width: 62%;
    }

    .ticket-header {
      margin-bottom: 8px;
    }

    .logo-img {
      height: 50px;
      margin-bottom: 20px;
    }

    .ticket-title {
      font-size: 16px;
      margin: 0;
      color: #1D428A;
      text-transform: uppercase;
    }

    .ticket-subtitle {
      margin: 5px 0 0;
      color: #555;
    }

    .ticket-table {
      width: 100%;
      font-size: 14px;
      color: #333;
    }

    .text-title-table{
      width: 25%;
    }

    .ticket-evento {
      background-color: #fff;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 15px;
      font-size: 14px;
      margin-top: 15px;
    }

    .ticket-evento p {
      margin: 5px 0;
    }

    .ticket-alert {
      font-size: 13px;
      color: #fd7b09;
      margin-top: 10px;
    }

    .ticket-divider {
      margin: 20px auto;
      width: 100%;
      max-width: 100%;
      border-top: 2px dashed #d6d6d6;
    }

    .ticket-qr {
      text-align: center;
      border-left: 2px dashed #1D428A;
    }

    .qr-img {
      margin-bottom: 10px;
    }

    .qr-note {
      font-size: 11px;
      color: #555;
      text-align: center;
    }

    /* INVITADO */
    .ticket-invitado {
      width: 100%;
      position: relative;
      max-width: 100%;
      margin: auto;
      background: white;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      page-break-inside: avoid;
      break-inside: avoid;
      margin-bottom: 45px;
    }

    .invitado-header {
      position: relative;
      background-color: #343a40;
      color: white;
      text-align: center;
      /* padding: 25px 20px 15px; */
      padding: 0px !important;
      overflow: hidden;
    }

    .invitado-header .logo-img {
      /* height: 45px; */
      /* margin-bottom: 15px; */
      width: 100%;
      max-width: 100%;
      height: 320px;
      display: block;
      margin-bottom: 0px;
      background-size: cover;
      padding: 0px;
    }

    .invitado-subtitle {
      margin: 5px 0 0;
      font-size: 13px;
      color: #ccc;
    }

    .invitado-info {
      padding: 25px 20px;
      font-size: 14px;

    }

    .invitado-info p {
      margin: 0 0 10px;
    }

    .invitado-qr {
      text-align: center;
      border-left: 2px dashed #1D428A;
      padding: 20px;
    }

    .invitado-footer {
      /* position: absolute; */
      bottom: 0;
      width: 100%;
      background-color: #1D428A;
      text-align: center;
      padding: 8px;
      color: white;
      font-size: 12px;
      letter-spacing: 1px;
    }
  </style>
</head>

<body class="body-bg">

  <!-- PARTICIPANTE -->
  <div class="ticket-participante">
    <table width="100%" style="border-left: 10px solid #1D428A;">
      <tr>
        <td class="ticket-info">
          <div class="ticket-header">
          {{--<img src="{{public_path('images/logo.png')}}" alt="Logo" class="light-logo logo-img" />--}}
          <img src="{{asset('images/logo.png')}}" alt="Logo" class="light-logo logo-img" />
            <h1 class="ticket-title">Evento exclusivo para participantes registrados</h1>
          </div>
          <div class="ticket-personal-data">
            <table class="ticket-table">
              <tr>
                <td class="text-title-table"><strong>Nombres:</strong></td>
                <td>{{ $cliente->nombres }} {{ $cliente->apellidopaterno }} {{ $cliente->apellidomaterno }}</td>
              </tr>
              @if (!empty($cliente->idcliente))
              <tr>
                <td class="text-title-table"><strong>Código Alumno:</strong></td>
                <td>{{ $cliente->idcliente }}</td>
              </tr>
              @endif

              @if (!empty($cliente->email))
              <tr>
                <td class="text-title-table"><strong>Email:</strong></td>
                <td>{{ $cliente->email }}</td>
              </tr>
              @endif

            </table>
          </div>
          <div class="ticket-evento">
            <p><strong>Fecha del Evento:</strong> {{ $cliente->sede_destino }}</p>

          </div>
        </td>
        <td class="ticket-qr">
          <img src="{{ $qryf }}" alt="QR Cliente" style="width: 200px;" class="qr-img">
          <p class="qr-note">La responsabilidad de este boleto es exclusiva del asistente. No compartir ni publicar.</p>
        </td>
      </tr>
    </table>
  </div>

</body>

</html>
