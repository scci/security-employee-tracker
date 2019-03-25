@extends('layouts.master')

@section('title', "$training->name")

@section('sidebar')
    @include('training._sidebar')
@stop

@section('content')

    @can('edit')
        <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large red tooltipped modal-trigger" href="{{ url("training/$training->id/assign") }}" data-position="left" data-tooltip="Assign training to users">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endcan

    <div class="row">
        <div class="col s12 m4">
            @include('training._sidebar')
        </div>
        <div class="col s12 m8">
            @if (session('status'))
                <script>
                    Materialize.toast("{{ @session('status') }}", 4000);
                </script>
            @endif

            <div class="card">
                <div class="card-content">
                    <span class="card-title">
                        {{ $training->name }}
                        <a href="{{ url("/training/$training->id/edit") }}" class="btn-flat btn-sm"><i class="material-icons">mode_edit</i></a>
                    </span>

                    <table class="row-border hover data-table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th data-hide="phone">Due Date</th>
                            <th>Completed</th>
                            <th class="sorter-false"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($notes as $trainingUser)
                            <tr class="tr{{$trainingUser->id}}">
                                <td class="training_name">
                                    <a href="{{ url('/user', $trainingUser->user->id) }}">{{ $trainingUser->user->userFullName }}</a>
                                </td>
                                <td class="training_due_date">{{ $trainingUser->due_date }}</td>
                                <td class="training_completed_date">{{ $trainingUser->completed_date }}</td>
                                <td width="180" class="right-align">
                                    @can('edit')
                                        @unless($trainingUser->completed_date)
                                        <button type="button" class="btn-flat btn-sm completed-today tooltipped" data-id="{{ $trainingUser->id }}" data-user="{{ $trainingUser->user_id }}" data-position="top" data-tooltip="Mark Completed Today">
                                            <i class="material-icons">done</i>
                                        </button>

                                        <a type="button" class="btn-flat btn-sm tooltipped" href="{{url('training/reminder', $trainingUser->id)}}" data-position="top" data-tooltip="Send Reminder">
                                            <i class="material-icons">email</i>
                                        </a>
                                        @endunless

                                        <span class="action-buttons">
                                        <a type="button" class="btn-flat btn-sm tooltipped" href="{{ action('TrainingUserController@edit', [$trainingUser->user_id, $trainingUser->id]) }}" data-position="top" data-tooltip="Edit">
                                            <i class="material-icons">mode_edit</i>
                                        </a>

                                        <button type="submit" class="btn-flat btn-sm delete-training-user tooltipped" data-record="tr{{$trainingUser->id}}" data-url="/user/{{$trainingUser->user_id}}/training/{{ $trainingUser->id }}" data-type="training" data-position="top" data-tooltip="Delete">
                                            <i class="material-icons">delete</i>
                                        </button>
                                        </span>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if ($showAll)
                        <a href="{{ url('training', $training->id) }}">Show Unique Users</a>
                    @else
                        <a href="{{ url('training', $training->id) }}?showAll=true">Show All Records</a>
                    @endif
                </div>
            </div>

        </div>
    </div>
@stop

@section('help')
    <strong>Completed Today</strong>
    <p>You may quickly update a user's record and mark their training to be completed for today by clicking the checkbox icon (<i class="material-icons">done</i>). This is usually best suited for marking multiple users at a time, such as taking role during a training/meeting.</p>

    <strong>Reminder Email</strong>
    <p>Click on the envelope icon to send out a remind email to the user. They will receive an email with the description, attachment and a link to complete that specific training.</p>

    <strong>Auto Renew</strong>
    <p>This function allows you to set the auto-renwal function such that the training will be automatically reassigned to be due for a person after X days has passed.</p>
    <p>Example: If you have a training that renews every year (365 days), and you have a user who completed the training 355 days ago, the system will automatically assign that user the same training to be done again. And will have the due date set for the 365th day.</p>

@stop
