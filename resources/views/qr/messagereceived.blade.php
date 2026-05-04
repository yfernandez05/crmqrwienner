<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email</title>
    <style>


        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            color: #333;
            padding: 20px;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0px;
            background-color: #00a7a6;
            border-radius: 10px;
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            margin: 10px auto;
        }
        h1, h2 {
            color: #2b52c2;
        }
        .qr-code {
            border-radius: 5px;
            display: block;
            margin-bottom: 0.8em;
            text-align: center;
            align-items: center;
        }
        .qr-code img, .qr-code svg {
            border-radius: 10px;
            display: block;
            margin: 0 auto;
            max-width: 200px !important;
            padding: 15px;
            background-color: white;
        }
        .qr-code h3{
            margin-bottom: 0px;
            color: white;
            text-shadow: 2px 2px 0px #ac604d;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
        .footer a {
            color: #01669d;
            text-decoration: none;
        }
        .cont-img img{
            width: 100%;
            max-width: 100%;
        }
        .cont-img{
            color: #00c6ff;
            font-family: system-ui;
            font-weight: 500;
            font-size: 2em;
        }
        .cont-img p{
            margin: 0;
        }
        .center{
            text-align: center;
        }

        .titulonombre{
               color: white;
                padding: 20px 100px;
                font-size: 25px;
                font-family: sans-serif;
                font-weight: 600;
        }

    </style>
</head>
<body>
<div class="container">
    <div class="cont-img">

         <!-- header -->
        <img src="{{ $message->embed('images/mail_headder.png') }}" alt="Gracias por Registrarte" class="img-fluid">
        <p class="titulonombre">Hola, {{ $cliente->nombres }},
            tu registro fue un éxito.
            Te esperamos el</p>
         <!-- sub body -->

        @if($cliente->colegio == "Lima Norte")
           <img src="{{ $message->embed('images/limanorte.png') }}" alt="limanorte" class="img-fluid">
        @else
            <img src="{{ $message->embed('images/limacentro.png') }}" alt="limacentro" class="img-fluid">
        @endif

        <img src="{{ $message->embed('images/tupase.png') }}" alt="tupase" class="img-fluid">
        <div class="qr-code mb-2">
            <img src="{!! $message->embedData($qrCode, 'QR_Feria_2024.png', 'image/png') !!}" alt="QR Code"> <!-- prod -->
        </div>

        <!-- footer -->
        <img src="{{ $message->embed('images/footer.png') }}" alt="footer" class="img-fluid">

    </div>

    </div>
    <div class="center">
        <!-- Pie de página -->
        <div class="footer">
            © Universidad Norbert Wiener {{ date('Y') }} - Todos los Derechos Reservados
            <a href="https://www.uwiener.edu.pe/" target="_blank"><strong>Universidad Norbert Wiener</strong></a>
        </div>
    </div>
</body>
</html>
