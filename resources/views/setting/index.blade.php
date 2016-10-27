@extends('layouts.master')

@section('title', 'Settings')

@section('content')

    <div class="card">
        <div class="card-content">
            <div class="card-title">Settings</div>

            {!! Form::open(array('action' => ['SettingController@update', 'id'=>'none'], 'method' => 'PUT', 'class' => 'container-fluid')) !!}

            <div class="row">
                <div class="col s6 m4 input-field">
                        {!! Form::label( 'report_address-primary', 'FSO Name:') !!}
                        {!! Form::text( 'report_address-primary', $report->primary ,['class' => 'form-control']) !!}
                </div>
                <div class="col s6 m4 input-field">
                        {!! Form::label('report_address-secondary', 'FSO Email:') !!}
                        {!! Form::text('report_address-secondary', $report->secondary ,['class' => 'form-control']) !!}
                </div>
                <div class="col s6 m4">
                        {!! Form::label('admin[]', 'Admin:') !!}
                        {!! Form::select('admin[]', $userList, $admins ,['class' => 'form-control', 'multiple', 'data-live-search' => "true"]) !!}
                        <small>From Config: {{ $configAdmins }}</small>
                </div>
                <div class="col s6 m4">
                        {!! Form::label('viewer[]', 'Viewer:') !!}
                        {!! Form::select('viewer[]', $userList, $viewers ,['class' => 'form-control', 'multiple', 'data-live-search' => "true"]) !!}
                </div>
            </div>
            <div class="row">
                <div class="col s12 right-align">
                        {!! Form::submit('Save', array('class' => 'btn-flat')) !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@stop

@section('help')
    <strong>FSO name and email</strong>
    <p>Used to send all outbound emails.</p>

    <strong>Admins & Viewers</strong>
    <p>Admins allow you to make any changes in the system. A user can also be made an admin by updating the <code>config/auth.php</code> file.</p>
    <p>Viewers allows you to see any pages in the system, but not change any data.</p>
    <p>Note: A user can either be an admin or a viewer, not both. If a user is assigned to both, the system will remove their admin status and make them a viewer.</p>

@stop
