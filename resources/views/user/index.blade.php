@extends('layouts.master')

@section('title', 'Users Directory')

@section('content')

    @can('edit')
        @if($userStatus != 'separated')
            <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
                <a class="btn-floating btn-large red tooltipped modal-trigger" href="{{ url('/user/create') }}" data-position="left" data-tooltip="New User">
                    <i class="large material-icons">add</i>
                </a>
            </div>
        @endif
    @endcan

    <div class="card">
        <div class="card-content">
            <span class="card-title">Users</span>            
            @if($userStatus == 'separated')
                <table class="row-border hover data-table">
                    <thead>
                        <tr>
                            <th>Name</th>                      
                            <th>Separated Date</th>                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="user-{{$user->id}}">                                
                                <td><a href="{{ url('/user', $user->id) }}">{{ $user->userFullName }}</a></td>
                                <td>{{ $user->separated_date }}</td>                            
                            </tr>
                        @endforeach
                    </tbody>
                </table>            
            @else
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
                                <td>@if(!$user->supervisor && $user->status != 'separated')
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
            @endif
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

    <strong>Edit/Update a User</strong>
    <p>You may edit/update a  user record using the <code>Edit</code> button towards the right of the user row. <br>
       Note: You may edit a separated user by clicking on the name of the user.
    </p>
    
    <strong>Deleting a User</strong>
    <p>You may delete a  user record using the <code>Delete</code> button towards the right of the user row.<br>
       Note: Once deleted a user record will be deleted from the database. This may have adverse effect if the user was an admin or trainings were assigned to the user. <br>
             Only delete users created accidentally and who have not been assigned trainings or groups or user roles.     
    </p>
    
    <strong>Users</strong>
    <p>LDAP users will automatically be pulled in, even if you manually delete them. As such, it is better to filter the users out via the config file.</p>
@stop
