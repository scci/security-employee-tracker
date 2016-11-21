<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ trans('messages.title') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('installer/img/favicon/favicon-16x16.png') }}" sizes="16x16"/>
    <link rel="icon" type="image/png" href="{{ asset('installer/img/favicon/favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ asset('installer/img/favicon/favicon-96x96.png') }}" sizes="96x96"/>
    <link href="{{ asset('installer/css/style.min.css') }}" rel="stylesheet"/>
    <script src="{{ url('installer/js/jquery-3.1.1.min.js') }}"></script>
  </head>
  <body>
    <div class="master">
      <div class="box">
        <div class="header">
            <h1 class="header__title">@yield('title')</h1>
        </div>
        <ul class="step">
          <li class="step__divider"></li>
          <li class="step__item {{ isActive('LaravelInstaller::final') }}">
            <a href="{{url('install/final')}}" title="Disable Installer"><i class="step__icon database"></i></a>
          </li>
          <li class="step__divider"></li>
          <li class="step__item {{ isActive('installAdmin') }}">
            <a href="{{url('install/user')}}" title="Admin User"><i class="step__icon user"></i></a>
          </li>
          <li class="step__divider"></li>
          <li class="step__item {{ isActive('LaravelInstaller::permissions') }}">
            <a href="{{url('install/permissions')}}" title="Directory Permissions"><i class="step__icon permissions"></i></a>
          </li>
          <li class="step__divider"></li>
          <li class="step__item {{ isActive('LaravelInstaller::requirements') }}">
            <a href="{{url('install/requirements')}}" title="PHP Requirements"><i class="step__icon requirements"></i></a>
          </li>
          <li class="step__divider"></li>
          <li class="step__item {{ isActive('LaravelInstaller::environment') }}">
            <a href="{{url('install/environment')}}" title="Environment Settings"><i class="step__icon update"></i></a>
          </li>
          <li class="step__divider"></li>
          <li class="step__item {{ isActive('LaravelInstaller::welcome') }}">
            <a href="{{url('install')}}" title="Welcome"><i class="step__icon welcome"></i></a>
          </li>
          <li class="step__divider"></li>
        </ul>
        <div class="main">
          @yield('container')
        </div>
      </div>
    </div>
  </body>
</html>