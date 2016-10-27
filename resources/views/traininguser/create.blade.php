@extends('layouts.master')

@section('title', 'Assign Training')


@section('content')

    <div class="card">
        <div class="card-content">
            <div class="card-title">Assign Training <small>({{ $user->userFullName }})</small></div>

            {!! Form::open(array('action' => ['TrainingUserController@store', $user->id], 'method' => 'POST', 'files' => true)) !!}
                @include('traininguser._form', ['submit' => 'Create'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')

    <h3>Assign Training</h3>

@stop
