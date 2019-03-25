<!DOCTYPE html>
<html>
<head>
    <title>SCCI SET</title>
    <link rel="icon" type="image/icon" href="{{url('favicon.ico')}}">

    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/icon?family=Material+Icons">

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <link rel="stylesheet" href="{{ url('/css/app.css') }}" />
    <script type="text/javascript" src="{{ url('/js/lib.js') }}"></script>
    <script type="text/javascript" src="{{ url('/js/custom.js') }}"></script>

    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        main {
            flex: 1 0 auto;
        }
    </style>
</head>
<body>

<header>
    <nav>
        <div class="nav-wrapper">
            <a class="brand-logo" href="{{url("/")}}"> <img src="{{url("/img/logo-white.png")}}" alt="Security Employee Tracker" /> <span data-toggle="tooltip" data-placement="bottom" title="Security Employee Tracker">SET</span></a>
            <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>

            @if(config('auth.providers.users.driver') != 'ldap')
                <ul class="right" id="nav-mobile">
                    <li><a href="{{url("register")}}">Register</a></li>
                </ul>
            @endif
        </div>
    </nav>
</header>
<main>
    <div class="wrapper">
        <div class="container">
            @yield('content')
        </div>
    </div>
</main>

<footer class="page-footer">
    <div class="container">
        <div class="row">
            <div class="col l6 s12">
                <h5 class="white-text">DOD Hotline</h5>
                <span class="grey-text text-lighten-4">Anyone may file a complaint with the DoD Hotline.<br />
                    Visit <a href="http://www.dodig.mil/hotline/">The Department of Defense Hotline Website</a> for more information.</span>
            </div>
            <div class="col l3 s12">
                <h5 class="white-text">Hotline Phone</h5>
                <span class="grey-text text-lighten-4">800-424-9098 (Toll-Free)<br />
                703-604-8799 (Commercial)</span>
            </div>
            <div class="col l3 s12">
                <h5 class="white-text">Hotline Fax</h5>
                <span class="grey-text text-lighten-4">
                    703-604-8567
                </span>
            </div>
        </div>
    </div>
    <div class="footer-copyright">
        <div class="container">
            Powered by SET &copy; 2015-<?php echo date("Y") ?>. An <a href="https://www.teamscci.com">SCCI</a> Product.
        </div>
    </div>
</footer>
</body>
</html>
