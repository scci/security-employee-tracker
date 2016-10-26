<p>{{ $user->userFullName }},</p>
<p>This is a reminder that you are in charge of the <strong>{{ $duty->name }}</strong> security check for
    @if($duty->cycle == 'monthly') the month starting
    @elseif($duty->cycle == 'weekly') the week starting
    @endif {{ $date }}
<p>If you cannot perform this function, please make arrangements to have it covered.</p>
<p><a href="{{ url('duty',$duty->id) }}">View the full schedule</a>.</p>