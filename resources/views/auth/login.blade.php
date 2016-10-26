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
        ul li {
            list-style-type: initial; margin-left: 30px;
        }
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
<main>
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <div class="col s12 m3 push-m9">
                    <div class="card">
                        <div class="card-content center-align">
                        <img id="profile-img" class="profile-img-card" src="{{ url('/img/logo.png') }}" />
                        <p id="profile-name" class="profile-name-card">Security Employee Tracker</p>



                        <form method="POST" action="{{ url('/login') }}" class="form-signin">
                            {!! csrf_field() !!}

                            <span id="reauth-email" class="reauth-email"></span>

                            <div class="input-field">
                                <label for="inputEmail">Windows Login</label>
                                <input type="text" id="inputEmail" name="username" class="validate" required>
                            </div>
                            <div class="input-field">
                                <label for="inputPassword">Password</label>
                                <input type="password" name="password" id="inputPassword" class="validate" required>
                            </div>
                            <button class="btn" type="submit">Sign in</button>

                            <div> <br />
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                            </div>

                        </form><!-- /form -->
                        </div>
                    </div>

                </div>
                <div class="col s12 m9 pull-m3">

                    <div class="card">
                        <div class="card-content">
                    <p>You are accessing a U.S. Government (USG) Information System (IS) that is provided for USG-authorized use only.</p>

                    <p>By using this IS (which includes any device attached to this IS), you consent to the following conditions:</p>
                    <ul class="browser-default">
                        <li>The USG routinely intercepts and monitors communications on this IS for purposes including, but not limited to, penetration testing, COMSEC monitoring, network operations and defense, personnel misconduct (PM), law enforcement (LE), and counterintelligence (CI) investigations.</li>
                        <li>At any time, the USG may inspect and seize data stored on this IS.</li>
                        <li>Communications using, or data stored on, this IS are not private, are subject to routine monitoring, interception, and search, and may be disclosed or used for any USG-authorized purpose.</li>
                        <li>This IS includes security measures (e.g., authentication and access controls) to protect USG interests--not for your personal benefit or privacy.</li>
                        <li>Notwithstanding the above, using this IS does not constitute consent to PM, LE or CI investigative searching or monitoring of the content of privileged communications, or work product, related to personal representation or services by attorneys, psychotherapists, or clergy, and their assistants. Such communications and work product are private and confidential. See User Agreement for details.</li>
                    </ul>
                        </div>
                    </div>

                </div>


        </div>
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
                <span class="grey-text text-lighten-4">800-424-9098(Toll-Free)<br />
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
</footer>
</body>
</html>