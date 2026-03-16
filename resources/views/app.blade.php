<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        /* Font Pacifico  */
        @font-face {
            font-family: "Epunda_Slab";
            src: url("/webfonts/Epunda_Slab/EpundaSlab-VariableFont_wght.ttf") format("truetype");
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: "Epunda_Slab";
            src: url("/webfonts/Epunda_Slab/EpundaSlab-Italic-VariableFont_wght.ttf") format("truetype");
            font-weight: 100 900;
            font-style: italic;
            font-display: swap;
        }

        /* Font Montserrat  */
        @font-face {
            font-family: "Montserrat";
            src: url("/webfonts/Montserrat/Montserrat-VariableFont_wght.ttf") format("truetype");
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: "Montserrat";
            src: url("/webfonts/Montserrat/Montserrat-Italic-VariableFont_wght.ttf") format("truetype");
            font-weight: 100 900;
            font-style: italic;
            font-display: swap;
        }
    </style>

    {{-- CSS --}}
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="app-blank layout-fixed layout-navbar-fixed">
    @inertia
</body>

</html>
