@extends('layouts.master')

@section('title', 'New Group')


@section('content')

    <div class="card" id="new-training-form">
        <div class="card-content">
            <div class="card-title">Edit Group</div>
            {!! Form::model($group, array('action' => ['GroupController@update', $group->id], 'method' => 'PATCH', 'class' => 'form-inline container-fluid group-form')) !!}
                @include('group._new_group', ['submit' => 'Update'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')

    @include('group._new_group_help')

@stop
