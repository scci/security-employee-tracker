@extends('layouts.master')

@section('title', 'Add A Visit')


@section('content')

    <div class="card">
        <div class="card-content">
            <div class="card-title">Add a Visit</div>

            {!! Form::open(array('action' => ['VisitController@store', $user->id], 'method' => 'POST')) !!}
                @include('visit._form', ['submit' => 'Create'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')

    <h3>Add a Visit</h3>

@stop
