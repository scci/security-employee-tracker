@extends('layouts.master')

@section('title', 'Home Page')

@section('content')

    <div class="col s12 m12 l6">
        <ul class="collection with-header calendar z-depth-1">
                <li class="collection-header">Calendar</li>
                @foreach($calendar as $cal)
                    <li class="collection-item">
                        <div class="date white-text {{ $cal['date'] == \Carbon\Carbon::today()->format('Y-m-d') ? 'green darken-1' : 'indigo' }}">
                            <div class="month">
                                {{ Carbon\Carbon::createFromFormat('Y-m-d', $cal['date'])->format('M') }}
                            </div>
                            <div class="day">
                                {{ Carbon\Carbon::createFromFormat('Y-m-d', $cal['date'])->format('j') }}
                            </div>
                        </div>
                        <div class="events">
                            @if(count($cal['separated']) > 0)
                                <div class="entry">
                                    The following records will be deleted: {!! implode("; ", array_map(function($a) {return "<a href='". url('user', $a['id']) . "'>". $a['last_name'] . ", ". $a['first_name'] ."</a>";}, $cal['separated'])) !!}
                                </div>
                            @endif
                            @foreach($cal['travel'] as $travelCalendar)
                                @if($travelCalendar->leave_date == $cal['date'])
                                    <div class="entry"> <a href="{{ url('user', $travelCalendar->user_id) }}">{{ $travelCalendar->user->userFullName }}</a> leaves for {{ $travelCalendar->location }}.</div>
                                @else
                                    <div class="entry"> <a href="{{ url('user', $travelCalendar->user_id) }}">{{ $travelCalendar->user->userFullName }}</a> returns from {{ $travelCalendar->location }}.</div>
                                @endif
                            @endforeach
                            @foreach($cal['trainingUser'] as $trainingUserCalendar)

                                <div class="entry"> <a href="{{ url('training', array_values($trainingUserCalendar)[0]->training_id) }}">{{ array_values($trainingUserCalendar)[0]->training->name }}</a> is due for
                                    @if(count($trainingUserCalendar) > 3)
                                        {{ count($trainingUserCalendar) }} people.
                                    @else
                                        {!! implode("; ", array_map(function($a) {return $a['userLink'];}, $trainingUserCalendar)) !!}
                                    @endif
                                </div>
                            @endforeach
                            @foreach($cal['newUser'] as $newUserCalendar)
                                <div class="entry"> <a href="{{ url('user', $newUserCalendar->id) }}">{{ $newUserCalendar->userFullName }}'s</a> account was created.</div>
                            @endforeach
                        </div>
                    </li>
                @endforeach
        </ul>

        @if($log->count())
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Recent User Profile Changes</span>
                    <table class="bordered">
                        <thead>
                        <tr><th>Modified</th><th>User</th><th>Note</th></tr>
                        </thead>
                        <tbody>
                        @foreach($log as $note)
                            <tr>
                                <td style="white-space: nowrap;">{{ $note->updated_at->format('Y-m-d') }}</td>
                                <td><a href="{{ url('user', $note->user_id) }}">{{ $note->user->userFullName }}</a></td>
                                <td>{{ $note->comment }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div class="col s12 m12 l6">

        @if ( Session::has('last_logon') )
            @include('home._last_login')
        @endif

        <div class="card">
            <div class="card-content">
                <span class="card-title">Today's Security Checks</span>
                <table class="bordered">
                    <tbody>
                    @foreach($duties as $duty)
                        @if($duty->groups->count() || $duty->users->count())
                        <tr>
                            <td>{{ $duty->name }}</td>
                            @if($duty->has_groups)
                                    <td>
                                        @foreach($duty->groups->first()->users as $user)
                                            <a href="{{ url('user', $user->id) }}">{{ $user->userFullName }}</a>@unless($loop->last); @endunless
                                        @endforeach
                                    </td>
                            @else
                                <td><a href="{{ url('user', $duty->users->first()->id) }}">{{ $duty->users->first()->userFullName }}</a></td>
                            @endif
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        @if($trainingUser->count())
        <div class="card">
            <div class="card-content">
                <span class="card-title">Recently Completed Training</span>
                <table class="bordered">
                    <thead>
                        <tr><th>Completed</th><th>User</th><th>Training</th></tr>
                    </thead>
                    <tbody>
                        @foreach($trainingUser as $note)
                            <tr>
                                <td style="white-space: nowrap;">{{ $note->completed_date }}</td>
                                <td><a href="{{ url('user', $note->user_id) }}">{{ $note->user->userFullName }}</a></td>
                                <td><a href="{{ url('training', $note->training_id) }}">{{ $note->training->name }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
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
