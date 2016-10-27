@extends('layouts.master')

@section('title', 'Group Directory')

@section('sidebar')
    <style>.sidebar {display:none;}</style>
@stop

@section('content')

    @can('edit')
        <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large red tooltipped modal-trigger" href="{{ url('/group/create') }}" data-position="left" data-tooltip="New Group">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endcan

    <ul class="collapsible popout" data-collapsible="accordion">
        <li><div class="collapsible-title">Groups</div></li>

        @foreach ($groups as $group)
                <li class="group-{{ $group->id }}">
                    <div class="collapsible-header">
                        <span class="right action-buttons">
                            <a href="{{ url("/group/$group->id/edit") }}" class="btn-flat btn-sm tooltipped" data-id="{{ $group->id }}" type="button" data-position="top" data-tooltip="Edit">
                                <i class="material-icons">mode_edit</i>
                            </a>

                            <button type="button" class="btn-flat btn-sm delete-group tooltipped" data-id="{{ $group->id }}" data-position="top" data-tooltip="Delete">
                                <i class="material-icons">delete</i>
                            </button>
                        </span>
                        <span class="blue-text">{{ $group->name }}</span>
                        @if($group->closed_area)
                            - has closed area
                        @endif
                    </div>
                    <div class="collapsible-body">
                        <div class="row">
                            <div class="col s12 m4">
                                Users: <br />
                                @foreach($group->users as $user)
                                    <a href="{{ url('/user', $user->id) }}" data-id="{{ $user->id }}">{{ $user->userFullName }}</a> <br/>
                                @endforeach
                            </div>
                            <div class="col s12 m4">
                                Training Subscriptions: <br />
                                @foreach($group->trainings as $training)
                                    <a href="{{ url('/training', $training->id) }}" data-id="{{ $training->id }}">{{ $training->name }}</a> <br/>
                                @endforeach
                            </div>
                            <div class="col s12 m4 right-align">
                                Last Modified: {{ $group->updated_at->format('y-m-d') }}
                            </div>
                        </div>
                    </div>
                </li>
        @endforeach
    </ul>

@stop

@section('help')
    <strong>Creating Groups</strong>
    <p>Create a group by clicking the New Group button on the bottom right of your screen.</p>

    <strong>Required Training</strong>
    <p>By creating a group with training, you can automatically assign all members several trainings if they have not already been assigned said training yet.</p>
    <p>For example, creating a training based on a contract and then assigning all training a user on that contract must complete. <br />
        If a user has not been assigned a required training, then they system will assign the training. Otherwise it will ignore the user in the group.</p>

    <strong>Closed Area</strong>
    <p>If a group is marked with a closed area, then you can assign access levels for the users in that group by editting the user's profile.</p>
@stop
