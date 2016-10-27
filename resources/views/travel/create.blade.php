@extends('layouts.master')

@section('title', 'Add A Travel')


@section('content')

    <div class="card" id="new-training-form">
        <div class="card-content">
            <div class="card-title">Add a Travel</div>

            {!! Form::open(array('action' => ['TravelController@store', $user->id], 'method' => 'POST', 'files' => true)) !!}
                @include('travel._form', ['submit' => 'Create'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')

    @include('travel._form_help')

@stop
