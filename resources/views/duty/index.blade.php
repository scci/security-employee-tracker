@extends('layouts.master')

@section('title', 'Duty Directory')


@section('content')

    @can('edit')
        <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large red tooltipped modal-trigger" href="{{ url('/duty/create') }}" data-position="left" data-tooltip="New Security Check">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endcan

    <div class="card">
        <div class="card-content">
            <div class="card-title">Security Check</div>

            <table class="bordered">
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Cycle</th>
                    <th></th>
                </tr>
                @foreach($duties as $duty)
                    <tr>
                        <td><a href="{{ url('duty', $duty->id) }}">{{ $duty->name }}</a></td>
                        <td>{{ $duty->has_groups ? 'Groups' : 'Individuals' }}</td>
                        <td>{{ ucfirst($duty->cycle) }}</td>
                        <td>
                            <div class="action-buttons">
                            @can('edit')
                                <a href="{{ url("/duty/$duty->id/edit") }}" class="btn-flat btn-sm tooltipped" data-position="top" data-tooltip="Edit">
                                    <i class="material-icons">mode_edit</i>
                                </a>
                                <button type="button" class="btn-flat btn-sm delete-duty tooltipped" data-id="{{ $duty->id }}" data-position="top" data-tooltip="Delete">
                                    <i class="material-icons">delete</i>
                                </button>
                            @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

@stop

@section('help')

    <strong>Add a Security Check</strong>
    <p>To create a new security check, click the red plus icon on the bottom right of the page.</p>

    <strong>Type</strong>
    <p>Type indicates how each entry is listed. They will either be group (multiple users per entry) or individual (single user per entry).</p>

    <strong>Cycle</strong>
    <p>Cycle indicates how often the security check will change to the next group/user. They include monthly, weekly and daily and will change at the begining of each (with weekly starting on Sunday).</p>

    <strong>Edit/Delete</strong>
    <p>Mouse over the row of the entry and click on the edit/delete icon that shows on hover.</p>

@stop
