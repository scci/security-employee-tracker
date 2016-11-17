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