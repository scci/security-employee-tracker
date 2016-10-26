@extends('layouts.master')

@section('title', 'Security Check Creation')


@section('content')

    <div class="card">
        <div class="card-content">
            <div class="card-title">Create Security Check</div>
            {!! Form::open(array('action' => 'DutyController@store', 'method' => 'POST')) !!}
            @include('duty._new_duty_form', ['submit' => 'Create'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')

    <h3>Creating a new security check</h3>

@stop