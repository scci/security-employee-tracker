@extends('layouts.master')

@section('title', "$user->first_name $user->last_name")

@section('content')

    <h2 style="margin-left:.5rem" class="hide-on-med-and-up">{{$user->userFullName}}</h2>

    <div class="col s12 m4">
        @include('user._sidebar')
    </div>

    <div class="col s12 m8">

        @if (session('status'))
            <script>
                Materialize.toast("{{ @session('status') }}", 4000);
            </script>
        @endif

        <ul class="collapsible popout" data-collapsible="accordion">
            @include('user._show_notes_blocks', ['alert' => true])
        </ul>

        <h2 style="margin-left:1.5rem" class="hide-on-small-only">{{$user->userFullName}}</h2>

        @foreach ($training_blocks as $block_title)
            @include('user._show_training_blocks',['sectionId' => $block_title])
        @endforeach
        @unless($training_user_types)
            @include('user._show_training_blocks',['sectionId' =>''])
        @endunless

        @include('user._show_travels_blocks')

        @include('user._show_visits_blocks')

        <ul class="collapsible popout" data-collapsible="accordion">
            <li>
                <div class="collapsible-title">Notes</div>
            </li>
                @include('user._show_notes_blocks', ['alert' => false])
            @can('edit')
                <li>
                    <div class="collapsible-footer right-align">
                        <a class="btn" href="{{ action('NoteController@create', $user->id) }}">Add Note</a>
                    </div>
                </li>
            @endcan
        </ul>

        @can('view')
            @include('user._show_logs_blocks')
        @endcan

    </div>

    <script type="text/javascript">
        @can('update_self', $user)
            var update_self = true;
        @else
            var update_self = false;
        @endcan
    </script>

@stop

@section('help')
    @can('edit')
        <strong>Update User Profile</strong>
        <p>You may use the pencil/edit icon next to the user status.</p>

        <strong>Delete a Record</strong>
        <p>To delete a record, click on the record so it expands. THen click the delete button.</p>

        <strong>Previous and Next User</strong>
        <p></p>
    @endcan

    <strong>User Documents</strong>
    <p>User documents are stored on the left column as their file name. If you need to add a title and/or a description, use the note section so you may label the user files. Notes also allows you to sticky the file to the top of the user page as well as hide it from everyone but yourself.</p>

    <strong>Update Record</strong>
    <p>To update a record, click on the record so it expands. Then click the edit button.</p>

    <strong>Remove an attachment</strong>
    <p>Open the record that you wish to edit and then click the x next to the attachment you wish to remove. <br />
    Note: Training attachments cannot be deleted from this page. @can('edit') To delete a training attachment, go to that trainin's page. Removing a training attachment removes it for all suers.@endcan</p>

    <strong>Active, separated and destroyed statuses</strong>
    <p>Each user may have one of 3 statuses:
    <ul>
        <li>Active (green): Account is currently active and the user will be listed in various select boxes in the application.</li>
        <li>Separated (yellow): Account has been deactivated. Person is no longer employed. Will be deleted from the system in 2 years.</li>
        <li>Destroyed (gray): Account has been flagged for deletion next week.</li>
    </ul>
    </p>
@stop
