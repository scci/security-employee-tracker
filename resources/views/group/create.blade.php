@extends('layouts.master')

@section('title', 'New Group')


@section('content')

    <div class="card" id="new-training-form">
        <div class="card-content">
            <div class="card-title">New Group</div>
            {!! Form::open(array('action' => 'GroupController@store', 'method' => 'POST')) !!}
                @include('group._new_group', ['submit' => 'Create'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')

    @include('group._new_group_help')

@stop
