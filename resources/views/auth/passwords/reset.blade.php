@extends('layouts.loggedout')

@section('content')
    <div class="row">
        <div class="col l4 offset-l4 m6 offset-m3 s12">
            <div class="card">
                <div class="card-content">
                    <div class="card-title">Reset Password</div>

                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="input-field">
                            <label for="email">E-Mail Address</label>
                            <input id="email" type="email" name="email" value="{{ $email or old('email') }}" required autofocus>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="input-field">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" required>

                            @if ($errors->has('password'))
                                <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="input-field">
                            <label for="password-confirm" >Confirm Password</label>
                            <input id="password-confirm" type="password" name="password_confirmation" required>

                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-flat right">
                            Reset Password
                        </button>

                        <br clear="both" />
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
