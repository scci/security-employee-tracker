@extends('layouts.master')

@section('title', 'Training Directory')


@section('content')

    <div class="card" id="new-training-form">
        <div class="card-content">
            <div class="card-title">Create Training</div>
            {!! Form::open(array('action' => 'TrainingController@store', 'method' => 'POST', 'files' => true, 'id' => 'new-training')) !!}
                @include('training._new_training', ['submit' => 'Create'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')
    @include('training._training_form_help')
@stop