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
