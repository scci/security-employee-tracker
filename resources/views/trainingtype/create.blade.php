@extends('layouts.master')

@section('title', 'Training Type Directory')

@section('content')

    <div class="card" id="new-trainingtype-form">
        <div class="card-content">
            <div class="card-title">Create Training Type</div>
            {!! Form::open(array('action' => 'TrainingTypeController@store', 'method' => 'POST',
                'id' => 'new-trainingtype')) !!}
                @include('trainingtype._trainingtype_form', ['submit' => 'Create'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')
    @include('trainingtype._form_help')
@stop
