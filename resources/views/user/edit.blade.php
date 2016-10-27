@extends('layouts.master')

@section('title', 'Training Directory')


@section('content')
    <div class="card" id="new-training-form">
        <div class="card-content">
            <div class="card-title">Edit User</div>

            {!! Form::model($user, array('action' => ['UserController@update', $user->id], 'method' => 'PATCH', 'class' => 'form container-fluid')) !!}
                @include('user._new_user', ['submit' => 'Update'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')
    @include('user._new_user_help')
@stop
