<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ticket</title>

    <style>
        * {
            font-family: monospace;
        }

        @page {
            margin: 5px;
        }

        body {
            font-size: 10px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            font-size: 9px;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }
    </style>
</head>

<body>
    @inject('configuracion', 'App\Models\Configuracion')

    <!-- ENCABEZADO -->
    <div class="center">
        <div class="bold">
            {{ $configuracion->first()->nombre_sistema }}
        </div>

        <div>
            Fecha: {{ date('d/m/Y H:i', strtotime($venta->fecha_hora)) }}
        </div>

        <div>
            Nota de Entrega
        </div>
    </div>

    <div class="divider"></div>

    <!-- DETALLE PRODUCTOS -->
    <table>
        <thead>
            <tr>
                <th class="left">Prod</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($venta->detalle_ventas as $item)
                <tr>
                    <td class="left">
                        {{ $item->item->codigo ? $item->item->codigo : $item->item->codigo_barras }}
                        {{ $item->item->nombre ? ' - ' . $item->item->nombre : '' }}
                    </td>

                    <td class="right">
                        {{ number_format($item->item->precio, 2) }}
                    </td>
                </tr>
                @php
                    $total += (float) $item->item->precio;
                @endphp
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- TOTAL -->
    <table>
        <tr>
            <td class="bold left">TOTAL</td>
            <td class="bold right">
                {{ number_format($total, 2) }}
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- MENSAJE FINAL -->
    <div class="center">
        ¡Gracias por tu compra!
    </div>

</body>

</html>
