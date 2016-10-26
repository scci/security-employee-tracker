@extends('layouts.master')

@section('title', 'Users Directory')

@section('content')

    @can('edit')
        <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large red tooltipped modal-trigger" href="{{ url('/user/create') }}" data-position="left" data-tooltip="New User">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endcan

    <div class="card">
        <div class="card-content">
            <span class="card-title">Users</span>
            <table class="row-border hover data-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Groups</th>
                        <th>Status</th>
                        <th>Incomplete</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="user-{{$user->id}}">


                            <td>@if(!$user->supervisor)
                                    <span class="tooltipped" data-position="right" data-tooltip="Missing Supervisor">
                                        <i class="material-icons orange-text">warning</i>
                                    </span>
                                @endif</td>
                            <td><a href="{{ url('/user', $user->id) }}">{{ $user->userFullName }}</a></td>
                            <td>
                                {{ $user->groups->implode('name', ', ') }}
                            </td>
                            <td>{{ $user->status }}</td>
                            <td>
                                @foreach($user->assignedTrainings as $assigned)
                                        <a href={{ url('/training', $assigned->training_id) }}>{{ $assigned->training->name }}</a> <br />
                                @endforeach
                            </td>
                            <td class="no-wrap">
                                <div class="action-buttons">
                                @can('edit')
                                    <a href="{{ url("/user/$user->id/edit") }}" class="btn-flat btn-sm tooltipped" data-position="top" data-tooltip="Edit">
                                        <i class="material-icons">mode_edit</i>
                                    </a>
                                    <button type="button" class="btn-flat btn-sm delete-user tooltipped" data-id="{{ $user->id }}" data-position="top" data-tooltip="Delete">
                                        <i class="material-icons">delete</i>
                                    </button>
                                @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.data-table').DataTable( {
                destroy: true,
                "order": [[ 1, "asc" ]],
                paging: false
            } );
        } );
    </script>
@stop

@section('help')
    <p>The users page displays a list of all the users in the system.</p>

    <strong>Creating a User</strong>
    <p>You may create a new user record using the <code>Add User</code> button on the bottom right of the page.</p>

    <strong>Users</strong>
    <p>LDAP users will automatically be pulled in, even if you manually delete them. As such, it is better to filter the users out via the config file.</p>
@stop