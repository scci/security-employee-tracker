@extends('layouts.loggedout')

@section('content')
    <div class="row">
        <div class="col l4 offset-l4 m6 offset-m3 s12">
            <div class="card">
                <div class="card-content">
                    <div class="card-title">Register</div>
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                        {{ csrf_field() }}

                        <div class="input-field">
                            <label for="first_name">First Name</label>
                            <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus />
                        </div>
                        @if ($errors->has('first_name'))
                            <span class="error">
                                <strong>{{ $errors->first('first_name') }}</strong>
                            </span>
                        @endif

                        <div class="input-field">
                            <label for="last_name">Last Name</label>
                            <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required />
                        </div>
                        @if ($errors->has('last_name'))
                            <span class="error">
                                <strong>{{ $errors->first('last_name') }}</strong>
                            </span>
                        @endif

                        <div class="input-field">
                            <label for="email">E-Mail Address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required />
                        </div>
                        @if ($errors->has('email'))
                            <span class="error">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif

                        <div class="input-field">
                            <label for="username">Username</label>
                            <input id="username" type="text" name="username" value="{{ old('username') }}" required />
                        </div>
                        @if ($errors->has('username'))
                            <span class="error">
                                <strong>{{ $errors->first('username') }}</strong>
                            </span>
                        @endif

                        <div class="input-field">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" required />
                        </div>
                        @if ($errors->has('password'))
                            <span class="error">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif

                        <div class="input-field">
                            <label for="password-confirm">Confirm Password</label>
                            <input id="password-confirm" type="password" name="password_confirmation" required />
                        </div>

                        <div class="input-field">
                            <label for="phone">Phone Number</label>
                            <input id="phone" type="text"  name="phone" />
                        </div>

                        <button type="submit" class="btn btn-flat right">
                            Register
                        </button>
                        <br clear="both" />

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
