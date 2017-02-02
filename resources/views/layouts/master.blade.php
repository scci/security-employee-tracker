<!DOCTYPE html>
<html>
<head>
    <title>{{ $app_name }} - @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" type="image/icon" href="{{url('favicon.ico')}}">
    <!-- Material Design fonts -->
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/icon?family=Material+Icons">

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <link rel="stylesheet" href="{{ url('/css/app.css') }}" />


    <script type="text/javascript">
        var root = '{{url("/")}}';
        @can('edit') var edit = 'true'; @endcan

        window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
        ]); ?>
    </script>

    <script type="text/javascript" src="{{ url('/js/lib.js') }}"></script>
    <script type="text/javascript" src="{{ url('/js/custom.js') }}"></script>

    <!--bring arrow_drop_down closer to dropdown-button -->
    <style>
        i.custom {margin-left: 0px;}
    </style>
</head>
<body>
    <header class="header @can('view')handle-action-item-sidebar @endcan">
        @include('layouts._navbar')
        @can('view')
            @include('layouts._sidebar_action_items')
        @endcan
    </header>
    @can('edit')
        @include('layouts._top_forms')
    @endcan
    <main class="container @can('view')handle-action-item-sidebar @endcan">
        <div class="row">
            @include('layouts._notifications')
            @yield('content')
        </div>
    </main>
    <footer class="page-footer @can('view')handle-action-item-sidebar @endcan" style="padding-top:0;">
        <div class="footer-copyright">
            <div class="container">
                Powered by SET &copy; 2015-<?php echo date("Y") ?>. An <a href="https://www.teamscci.com">SCCI</a> Product.
                @can('view')
                    <a href="https://github.com/scci/security-employee-tracker/wiki" class="right btn small">Documentation</a>
                    <a href="https://github.com/scci/security-employee-tracker/issues" class="right btn small">Report Bugs</a>
                @endcan
            </div>
        </div>
    </footer>


    <!-- Modal -->
    <div class="modal modal-fixed-footer" id="help">
            <div class="modal-content">
                <h4>Page Help</h4>
                @yield('help')
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect btn-flat ">Close</a>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>

</body>
</html>
