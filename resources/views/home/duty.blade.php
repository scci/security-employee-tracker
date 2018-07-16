<div class="card">
    <div class="card-content">
        <span class="card-title">Today's Security Checks</span>
        <table class="bordered">
            <tbody>
            @foreach($duties as $duty)
                <tr>
                    <td>{{ $duty['duty'] }}</td>
                    <td>{!! $duty['user'] !!}</td>
                </tr>                 
            @endforeach
            </tbody>
        </table>
    </div>
</div>