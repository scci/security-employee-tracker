@extends('layouts.master')

@section('title', 'Update Training')


@section('content')

    <div class="card">
        <div class="card-content">
            <div class="card-title">Update Training <small>({{$user->userFullName}})</small></div>
            {!! Form::model($trainingUser, array('action' => ['TrainingUserController@update', $user->id, $trainingUser->id], 'method' => 'PATCH', 'files' => true)) !!}
                @include('traininguser._form', ['submit' => 'Update'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')

    <h3>Update a Training</h3>

@stop
