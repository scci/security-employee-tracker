@extends('layouts.master')

@section('title', "Complete ". $trainingUser->training->name )

@section('sidebar')
@stop

@section('content')
    <div class="card">
        <div class="card-content">
            <div class="card-title">Complete {{ $trainingUser->training->name }}</div> <br />
            {!! Form::model($trainingUser, array('action' => ['TrainingUserController@update', $user->id, $trainingUser->id], 'method' => 'PATCH', 'files' => true)) !!}
                @include('traininguser._email_form')
            {!! Form::close() !!}
        </div>
    </div>


@stop

@section('help')

@stop
