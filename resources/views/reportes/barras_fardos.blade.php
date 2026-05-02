<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Barras</title>
    <style type="text/css">
        * {
            font-family: sans-serif;
        }


        @page {
            margin: 0;
            padding: 0;
        }

        .etiqueta {
            /* width: 58mm; */
            padding-top: 10px;
            text-align: center;
            margin: 0px;
            padding: 10px
        }

        .etiqueta img {
            max-width: 100%;
        }

        .nombre {
            font-size: 9pt;
        }

        .codigo {
            margin: 0px;
            font-size: 10pt;
        }

        .nueva_pagina {
            page-break-before: always;
        }
    </style>
</head>

<body>
    @php
        $cont = 0;
    @endphp
    @foreach ($fardos as $key => $p)
        <div class="etiqueta">
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($p->codigo_barras, 'C128', 1.67, 75) }}" />
            <div class="codigo">{{ $p->nombre }}</div>
            <div class="codigo">{{ $p->codigo_barras }}</div>
        </div>
        @php
            $cont++;
        @endphp
        @if ($cont < count($fardos) - 1)
            <div class="nueva_pagina"></div>
        @endif
    @endforeach
</body>

</html>
