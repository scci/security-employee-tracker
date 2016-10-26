<ul class="collapsible popout">
    <li><div class="collapsible-title">Logs</div></li>

    @foreach ($logs as $log)
            <li>
                <div class="collapsible-header">
                    {{--<div class="right">Recorded: {{ $log->created_at->format('Y-m-d') }}</div>--}}
                    {!! nl2br(e($log->comment)) !!}
                </div>
            </li>
    @endforeach
</ul>