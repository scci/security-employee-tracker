<div class="card-panel">
    <div class="card-content">
        @can('edit')<div class="right"><a href="{{ url("/user/$user->id/edit") }}" class="btn-flat btn-sm white-text"><i class="material-icons">mode_edit</i></a></div>@endcan
        <div class="card-title white-text darken-2 {{ $user->status == 'active' ? 'green' : ($user->status == 'separated' ? 'orange' : 'grey') }}">{{ strtoupper($user->status) }}</div>

        <div>
            <strong>Employee ID:</strong>
            {{ $user->emp_num }}</div>
        <div>
            <strong>Name:</strong>
            {{ $user->userFullName }}
            </div>
        <div>
            <strong>Email:</strong>
            {{ $user->email }}</div>
        <div>
            <strong>Phone:</strong>
            {{ $user->phone }}</div>
        <div>
            <strong>Supervisor:</strong>
             @if($user->supervisor)
                @can('view')<a href="{{ url('/user', $user->supervisor_id) }}">@endcan
                    {{ $user->supervisor->userFullName }}
                @can('view')</a>@endcan
            @endif
        </div>
        <div>
            <strong>Security Checks:</strong>
            @forelse($duties as $duty)
                <a href="{{ url('/duty', $duty->id) }}" >{{$duty->name}}</a>@unless($loop->last), @endunless
            @empty
                None
            @endforelse
        </div>
        @if($user->access_level)
            <div>
                <strong>Access Level:</strong>
                {{ $user->access_level }}
            </div>
        @endif
        <div class="divider"></div>
        <div>
            <strong>Clearance:</strong>
            {{ $user->clearance }}
        </div>
        <div>
            <strong>Eligibility Date:</strong>
            {{ $user->elig_date }}
        </div>
        <div>
            <strong>Investigation:</strong>
            {{ $user->inv }}
        </div>
        <div>
            <strong>Investigation Date:</strong>
            {{ $user->inv_close }}
        </div>

        @if($user->groups->count())
            <div class="divider"></div>
            <div>
                <strong>Groups:</strong>
                <ul class="browser-default" style="margin:0">
                    @foreach($user->groups as $group)
                        <li>{{ $group->name }}
                        @if($group->closed_area)
                            - {{ $group->pivot->access ?: 'No Access' }}
                        @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($user->subordinates->count())
            <div class="divider"></div>
            <div>
                <strong>Subordinates:</strong>
                <ul class="browser-default" style="margin:0">
                    @foreach($user->subordinates as $sub)
                        <li>
                            @can('view')<a href="{{ url('/user', $sub->id) }}">@endcan
                            {{ $sub->userFullName }}
                            @can('view')</a>@endcan
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        @can('view')
            <div class="divider"></div>
            @if($next)<span class="right"><a href="{{ url("user/$next") }}">Next User &gt;</a></span>@endif
            @if($previous)<a href="{{ url("user/$previous") }}">&lt; Previous User</a> @else &nbsp; @endif
        @endcan
    </div>
</div>

{{--Files--}}
<ul class="user collection with-header z-depth-1">
    <li class="collection-header" style="font-size: 24px; font-weight: 300;">Documents
        <a tabindex="0" role="button" data-trigger="focus" class="pull-right tooltipped"  data-position="top" data-tooltip="User Documents are encrypted by default as they may contain PII." aria-hidden="true">
            <i class="material-icons pull-right">live_help</i>
        </a>
    </li>
    @foreach($user->attachments as $file)
        <li class="collection-item">
            {!! Form::open(array('action' => ['AttachmentController@destroy', $file->id], 'method' => 'DELETE')) !!}
            <div class="file">
                <button type="submit" class="btn-floating waves-effect secondary-content">
                    <i class="material-icons">delete</i>
                </button>
                <a style="display:inline-block; max-width:calc(100% - 40px); word-wrap: break-word;" href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }} - <small>{{ $file->created_at->format('Y-m-d') }}</small></a>
            </div>
            {!! Form::close() !!}
        </li>
    @endforeach

    @can('edit')
    <li class="collection-item">
        {!! Form::open(array('action' => ['AttachmentController@store'], 'method' => 'POST', 'files' => true, 'class' => 'form-inline', 'id' => 'attachments-form')) !!}
        {!! Form::hidden('type', 'user') !!}
        {!! Form::hidden('id', $user->id) !!}
        {!! Form::multipleFiles('js-upload') !!}
        {!! Form::close() !!}
    </li>
    @endcan
</ul>