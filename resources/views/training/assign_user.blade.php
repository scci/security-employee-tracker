@extends('layouts.master')

@section('title', 'Assign Users')


@section('content')

    <div class="card" id="new-training-form">
        <div class="card-content">
            <div class="card-title">Assign users to {{$training->name}}</div>
            {!! Form::open(array('route' => ['training.assign', $training->id], 'method' => 'POST', 'class' => 'form container-fluid assign_user')) !!}
            <div class="row">
                <div class="col s12 input-field" id="group_field">
                    {!! Form::selectBox('Group', 'groups[]', $groups, true) !!}
                    {!! Form::label('groups[]', 'Groups:') !!}
                </div>
                <div class="col s12 input-field" id="user_field">
                    {!! Form::selectBox('User', 'users[]', $users, true) !!}
                    {!! Form::label('users[]', 'Users:') !!}
                </div>
                <div class="col s12 m6" id="due_date">
                    {!! Form::label('due_date', 'Due date:') !!}
                    {!! Form::date('due_date', \Carbon\Carbon::now(), array('class' => 'datepicker')) !!}
                </div>
                <div class="col s12 m6" id="completed_date">
                    {!! Form::label('completed_date', 'Completed date:') !!}
                    {!! Form::date('completed_date', null, array('class' => 'datepicker')) !!}
                </div>
            </div>
            <div class="row">
                <div class="col s12 right-align">
                    {!! Form::reset('Reset', array('class' => 'btn-flat waves-effect waves-indigo', 'id' => 'training-reset')) !!}
                    {!! Form::submit('Submit', array('class' => 'btn-flat waves-effect waves-indigo')) !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')

    <h3>Assigning Training to a User.</h3>


@stop