@extends('layouts.master')

@section('title', 'Bulk Update Training')


@section('content')

    <div class="card" id="new-training-form">
        <div class="card-content">
            <div class="card-title">Bulk Update {{$training->name}}</div>
            {!! Form::open(array('route' => ['training.bulkupdate', $training->id], 'method' => 'POST', 'files' => true, 'class' => 'form container-fluid bulk_update')) !!}
            <div class="row">
                <div class="col s12 m8 input-field" id="user_field">
                    {!! Form::selectBox('User', 'users[]', $incompleteUsers, true) !!}
                    {!! Form::label('users[]', 'Users:') !!}
                </div>
                <!--div class="col s12 input-field" id="user_field">
                    {!! Form::select('$users[]', [null => 'None'] + $users, $incompleteUsers, ['multiple']) !!}
                    {!! Form::label('users[]', 'Users:') !!}
                </div-->
                <div class="col s12 m4" id="completed_date">
                    {!! Form::label('completed_date', 'Completed date:') !!}
                    {!! Form::date('completed_date', null, array('class' => 'datepicker')) !!}  
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6" id="file_upload">
                    {!! Form::multipleFiles() !!}
                </div>
                <div class="col s12 m3">
                    <p style="margin-top: 2em">
                        {!! Form::hidden('encrypt', false) !!}
                        <input type="checkbox" name="encrypt" value=1 class="filled-in" id="encrypt" />
                        <label for="encrypt">File has PII/Encrypt File.</label>
                    </p>
                </div>
                <div class="col s12 m3">
                    <p style="margin-top: 2em">
                        {!! Form::hidden('admin_only', false) !!}
                        <input type="checkbox" name="admin_only" value=1 class="filled-in" id="admin_only" />
                        <label for="admin_only">Administrative File.</label>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col s12 input-field" id="training_textarea">
                    {!! Form::label('comment', 'User Notes:') !!}
                    {!! Form::textarea('comment', $trainingUser->comment ?? '', array('class' => 'materialize-textarea')) !!}
                </div>
            </div>
            <div class="row">
                <div class="col s12 right-align">
                    {!! Form::submit('Update', array('class' => 'btn-flat waves-effect waves-indigo')) !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')
    @include('training._bulk_update_help')    
@stop
