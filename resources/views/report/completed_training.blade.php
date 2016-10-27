<table border="1">
    <tr>
            <th>Employee</th>
        @foreach($trainings as $training)
            <th>{{ $training->name }}</th>
        @endforeach
    </tr>
    @foreach($users as $user)
        <tr>
            <td style="white-space: nowrap;">{{ $user->userFullName }}</td>
            @foreach($trainings as $training)
                <td>
                @foreach($user->assignedTrainings as $note)
                    @if($training->id == $note->training_id)
                        {{$note->completed_date}}
                        @break
                    @endif
                @endforeach
                </td>
            @endforeach
        </tr>
    @endforeach
</table>
