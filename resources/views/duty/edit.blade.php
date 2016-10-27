@extends('layouts.master')

@section('title', 'Security Check Update')


@section('content')

    <div class="card">
        <div class="card-content">
            <div class="card-title">Update Security Check</div>
            {!! Form::model($duty, array('action' => ['DutyController@update', $duty->id], 'method' => 'PATCH')) !!}
            @include('duty._new_duty_form', ['submit' => 'Update'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')

    <h3>Updating a Security Check</h3>

@stop
