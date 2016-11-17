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