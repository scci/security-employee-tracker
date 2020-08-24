@extends('layouts.master')

@section('title', 'Administration Settings')

@section('content')

    <div class="card">
        <div class="card-content">
            <div class="card-title">Administration Settings</div>

            {!! Form::open(array('action' => ['SettingController@update', 'id'=>'none'], 'method' => 'PUT', 'class' => 'container-fluid')) !!}

            <ul class="tabs">
                <li class="tab"><a href="#general">General</a></li>
                <li class="tab"><a href="#ldap">AD/LDAP</a></li>
                <li class="tab"><a href="#mail">Mail</a></li>
                <li class="tab"><a href="#users">Users</a></li>
            </ul><br />

            <div id="general">@include('setting._general')</div>
            <div id="ldap">@include('setting._adldap')</div>
            <div id="mail">@include('setting._mail')</div>
            <div id="users">@include('setting._users')</div>

            <div class="row">
                <div class="col s12 right-align">
                    {!! Form::submit('Save', array('class' => 'btn-flat waves-effect waves-indigo')) !!}
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
    
    <Strong>Access Tokens</Strong>
    <p>Add new Access Tokens to SET under the USERS tab. After a new token is created, it will be available to assign to users via their profile</p>
    <p>Note: A user can either be an admin or a viewer, not both. If a user is assigned to both, the system will remove their admin status and make them a viewer.</p>

@stop
