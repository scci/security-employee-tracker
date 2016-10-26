@extends('layouts.master')

@section('title', 'Update A Travel')


@section('content')

    <div class="card" id="new-training-form">
        <div class="card-content">
            <div class="card-title">Update a Travel</div>
            {!! Form::model($travel, array('action' => ['TravelController@update', $user->id, $travel->id], 'method' => 'PATCH', 'files' => true)) !!}
                @include('travel._form', ['submit' => 'Update'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')

    @include('travel._form_help')

@stop