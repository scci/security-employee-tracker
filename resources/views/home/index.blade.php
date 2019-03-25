@extends('layouts.master')

@section('title', 'Home Page')

@section('content')

    <div class="col s12 m12 l6">
        @include('home.calendar')

        @if($trainingUser->count())
            @include('home.completed_training')
        @endif
    </div>

    <div class="col s12 m12 l6">

        @if (session('status'))
            <script>
                Materialize.toast("{{ @session('status') }}", 4000);
            </script>
        @endif

        @if ( Session::has('last_logon') )
            @include('home._last_login')
        @endif

        @include('home.duty')

        @if($activityLog->count())
            @include('home.user_changes')
        @endif

    </div>

@stop

@section('help')

    <strong>Returning to this page</strong>
    <p>You may click on the logo/set name on the top left of the page to return here at any time.</p>

    <strong>Calendar</strong>
    <p>The calendar shows 9 weeks of data starting from the previous week. It lists the following:
    <ul class="browser-default">
        <li>Users who are marked as Separated and will be deleted from the database.</li>
        <li>Users who are leaving and returning (due to Travel record).</li>
        <li>Training Due and who/how many people have not completed the training.</li>
        <li>Users who were created in the application.</li>
    </ul>
    </p>

    <strong>Today's Security Checks</strong>
    <p>This section lists all users who are currently working a security check.</p>

    <strong>Recent User Profile Changes</strong>
    <p>List of changes made to the user profile over the last week.</p>

    <strong>Recently Completed Training</strong>
    <p>Training completed in the last week. While ordered by last updated, it lists what the user has marked as their complete date.</p>

@stop
