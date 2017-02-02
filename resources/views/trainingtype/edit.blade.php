@extends('layouts.master')

@section('title', 'Training Type Directory')

@section('content')
    <div class="card" id="edit-trainingtype-form">
        <div class="card-content">
            <div class="card-title">Edit Training Type</div>
            {!! Form::model($trainingtype, array('action' => ['TrainingTypeController@update', $trainingtype->id],
                'method' => 'PATCH', 'id' => 'edit-trainingtype')) !!}
                @include('trainingtype._trainingtype_form', ['submit' => 'Update'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')
    @include('trainingtype._form_help')
@stop
