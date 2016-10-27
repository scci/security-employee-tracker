<p>{{ $user->userFullName }},</p>
<p>This is a reminder that
    @if($duty->cycle == 'monthly')this month
    @elseif($duty->cycle == 'weekly')this week
    @else today
    @endif
    you are in charge of the <strong>{{ $duty->name }}</strong> security check.</p>
<p>If you cannot perform this function, please make arrangements to have it covered.</p>
<p><a href="{{ url('duty', $duty->id) }}">View the full schedule</a>.</p>
