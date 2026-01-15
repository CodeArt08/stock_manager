<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GSC</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('sweetalert2/sweetalert2.min.css') }}">
    @vite('resources/js/app.js') <!-- Ajoutez ici pour charger les scripts JS -->
</head>
<body>
    <div id="app">
        @include('partials.alerts') <!-- Pour les notifications et alertes -->

        <div class="container">
            <div class="containt1">
                <img src="{{ asset('images/logos/logoCisco.jpg')}}" alt="" width="60px" height="60px" class="img">
                
                <!-- Sidebar avec navigation via Vue Router -->
                <div class="sidebar">
                    <a href="/" class="f1" @click.prevent="$router.push('/')">
                        <img src="{{ asset('images/logos/file.png') }}" width="25px" height="25px" class="img2">
                        <span class="span1">Stock</span>
                    </a>
                    <a href="/liste-dist" class="f1" @click.prevent="$router.push('/liste-dist')">
                        <img src="{{ asset('images/logos/paper-airplane.png') }}" width="25px" height="25px" class="img2">
                        <span class="span1">Distribution</span>
                    </a>
                    <a href="/liste-zap" class="f1" @click.prevent="$router.push('/liste-zap')">
                        <img src="{{ asset('images/logos/map-pin.png') }}" width="25px" height="25px" class="img2">
                        <span class="span1">ZAP</span>
                    </a>
                    <a href="/liste-etab" class="f1" @click.prevent="$router.push('/liste-etab')">
                        <img src="{{ asset('images/logos/bird.png') }}" width="25px" height="25px" class="img2">
                        <span class="span1">Etablissement</span>
                    </a>
                    <a href="/liste-historique" class="f1" @click.prevent="$router.push('/liste-historique')">
                        <img src="{{ asset('images/logos/history.png') }}" width="25px" height="25px" class="img2">
                        <span class="span1">Historique</span>
                    </a>
                </div>

                <!-- Bouton de déconnexion -->
                <a href="/" class="exit" @click.prevent="$router.push('/')">
                    <img src="{{ asset('images/logos/power.png') }}" width="30px" height="30px" class="img2">
                    <span class="logout">exit</span>
                </a>
            </div>

            <!-- Le contenu du routeur Vue s'affichera ici -->
            <div class="containt2">
                <router-view></router-view>
            </div>
        </div>
    </div>
</body>
</html>
