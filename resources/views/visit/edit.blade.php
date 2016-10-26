@extends('layouts.master')

@section('title', 'Update A Visit')


@section('content')

    <div class="card">
        <div class="card-content">
            <div class="card-title">Update a Visit</div>
            {!! Form::model($visit, array('action' => ['VisitController@update', $user->id, $visit->id], 'method' => 'PATCH')) !!}
                @include('visit._form', ['submit' => 'Update'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')

    <h3>Update a Visit</h3>

@stop