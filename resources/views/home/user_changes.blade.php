<div class="card">
    <div class="card-content">
        <span class="card-title">Recent User Profile Changes</span>
        <table class="bordered">
            <thead>
            <tr><th>Modified</th><th>User</th><th>Note</th></tr>
            </thead>
            <tbody>
            @foreach($activityLog as $log)
                <tr>
                    <td style="white-space: nowrap;">{{ $log['updated_at']->format('Y-m-d') }}</td>
                    <td>{{ $log['user_fullname'] }}</td>
                    <td>{{ ucfirst(str_replace("_"," ",$log['comment'])) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
