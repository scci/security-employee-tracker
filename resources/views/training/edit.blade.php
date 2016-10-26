@extends('layouts.master')

@section('title', 'Training Directory')


@section('content')
    <div class="card" id="new-training-form">
        <div class="card-content">
            <div class="card-title">Edit Training</div>
            {!! Form::model($training, array('action' => ['TrainingController@update', $training->id], 'method' => 'PATCH', 'files' => true, 'id' => 'new-training')) !!}
                @include('training._new_training', ['submit' => 'Update'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')
    @include('training._training_form_help')
@stop