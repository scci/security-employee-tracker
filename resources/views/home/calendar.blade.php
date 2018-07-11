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