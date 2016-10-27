@extends('layouts.master')

@section('title', 'Create User')


@section('content')

    <div class="card" id="new-training-form">
        <div class="card-content">
            <div class="card-title">Create User</div>

            {!! Form::open(array('action' => 'UserController@store', 'method' => 'POST', 'class' => 'form container-fluid')) !!}
                @include('user._new_user', ['submit' => 'Create'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')
    @include('user._new_user_help')
@stop
