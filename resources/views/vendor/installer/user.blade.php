@extends('vendor.installer.layouts.master')

@section('title', trans('messages.user.title'))
@section('container')
    @if (session('message'))
        <p class="alert">{{ session('message') }}</p>
    @endif
    <form method="post" action="{{ url('install/user') }}">
        {!! csrf_field() !!}

        {!! Form::label('first_name', 'First Name') !!}
        {!! Form::text('first_name') !!}

        {!! Form::label('last_name', 'Last Name') !!}
        {!! Form::text('last_name') !!}

        {!! Form::label('email', 'Email') !!}
        {!! Form::email('email') !!}

        {!! Form::label('username', 'Username') !!}
        {!! Form::text('username') !!}

        {!! Form::label('password', 'Password') !!}
        {!! Form::password('password') !!}

        {!! Form::label('password_confirmation', 'Confirm Password') !!}
        {!! Form::password('password_confirmation') !!}

        <div class="buttons">
            <button class="button" type="submit">{{ trans('messages.user.save') }}</button>
        </div>
    </form>
@stop