@extends('layouts.master')

@section('title', 'Training Directory')

@section('content')

    @can('edit')
        @if (!$isTrainingType)
            <div class="fixed-action-btn" style="bottom: 45px; right: 100px;">
                <a class="btn-floating btn-large amber tooltipped modal-trigger" href="{{ url('/trainingtype') }}"
                    data-position="left" data-tooltip="Manage Training Types">
                    <i class="large material-icons">mode_edit</i>
                </a>
            </div>
        @endif
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
                        @if ($hasTrainingType)
                            <th>Type</th>
                        @endif
                        <th>Incomplete</th>
                        <th>Completed</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trainings as $training)
                        <tr class="training-{{$training->id}}">
                            <td><a href="{{ url('/training', $training->id) }}">{{ $training->name }}</a></td>
                            @if ($hasTrainingType)
                                @if ($training->trainingtype)
                                  <td><a href="{{ url('/trainingtype', $training->trainingtype->id) }}">{{ $training->trainingtype->name }}</a></td>
                                @else
                                  <td></td>
                                @endif
                            @endif
                            <td class="text-right">
                                @if($training->users->count())
                                    {{ $training->incompleted }}/{{$training->users->groupby('id')->count()}}
                                @else
                                    0/0
                                @endif
                            </td>
                            <td>
                                @if($training->users->count() && $training->users->count())
                                    {{ (int)( 100 - ( $training->incompleted / $training->users->groupby('id')->count() ) * 100)}}%
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

    <strong>Incomplete Column</strong>
    <p>This column indicates a ratio of the number of completed training over the
        number of assigned training that has not been completed.</p>

    <strong>Completed Column</strong>
    <p>This column indicates the percentage of completed training over the assigned training.
        If there is no outstanding training to be completed, it is marked 100%. </p>

    @if (!$isTrainingType)
        <strong>Managing training types</strong>
        <p>To manage training types, click on the Manage Training Type button (big amber button) on the bottom right of your screen.
            The Manage Training Type button is available from the Training->All option</p>
    @endif

    <strong>Creating a new training</strong>
    <p>To create a new training, click on the New Training button (big red button) on the bottom right of your screen.</p>

@stop
