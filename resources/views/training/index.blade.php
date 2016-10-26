@extends('layouts.master')

@section('title', 'Training Directory')


@section('content')

    @can('edit')
        <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large red tooltipped modal-trigger" href="{{ url('/training/create') }}" data-position="left" data-tooltip="New Training">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endcan

    <div class="card">
        <div class="card-content">
            <span class="card-title">Training, Credentials and Briefings</span>
            <table class="row-border hover data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Incomplete</th>
                        <th>Completed</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trainings as $training)
                        <tr class="training-{{$training->id}}">
                            <td><a href="{{ url('/training', $training->id) }}">{{ $training->name }}</a></td>
                            <td class="text-right">
                                @if($training->users->count())
                                    {{ $training->incompleted }}/{{$training->users->count()}}
                                @else
                                    0/0
                                @endif
                            </td>
                            <td>
                                @if($training->users->count() && $training->users->count())
                                    {{ (int)( 100 - ( $training->incompleted / $training->users->count() ) * 100)}}%
                                @else
                                    100%
                                @endif
                            </td>
                            <td class="no-wrap">
                                <div class="action-buttons">
                                @can('edit')
                                    <a href="{{ url("/training/$training->id/edit") }}" class="btn-flat btn-sm tooltipped" data-position="top" data-tooltip="Edit">
                                        <i class="material-icons">mode_edit</i>
                                    </a>
                                    <button type="button" class="btn-flat btn-sm delete-training tooltipped" data-id="{{ $training->id }}" data-position="top" data-tooltip="Delete">
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
@stop

@section('help')

    <strong>Sorting</strong>
    <p>You may sort any column by clicking on the column header.</p>

    <strong>Searching</strong>
    <p>When searching, the table will automatically hide any rows that are not part of the search criteria. All columns are searched.</p>

    <strong>Complete Column</strong>
    <p>This column indicates the number of assigned training that has not been completed. If there is no outstanding training to be completed, it is marked with a check. </p>

    <strong>Creating a new training</strong>
    <p>To create a new training, click on the New Training button (big red button) on the bottom right of your screen.</p>

    <strong>Making edits</strong>
    <p>You may attach and remove files via the file button on the left. Changes to the name, description and review dates (along with adding more attachments) MUST be done via editing the training (edit icon next to the training name).</p>


@stop