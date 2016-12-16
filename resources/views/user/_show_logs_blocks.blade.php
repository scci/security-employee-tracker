<ul class="collapsible popout">
    <li><div class="collapsible-title">Logs</div></li>

    @foreach ($activityLog as $log)
            <li>
                <div class="collapsible-header">
                    {{--<div class="right">Recorded: {{ $log['updated_at']->format('Y-m-d h:i:s') }}</div>--}}
                    {!! nl2br(e( ucfirst(str_replace("_"," ",$log['comment'])) )) !!}
                </div>
            </li>
    @endforeach
</ul>
