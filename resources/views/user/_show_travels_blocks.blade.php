<ul class="collapsible popout" data-collapsible="accordion">
    <li>
        <div class="collapsible-title">Travels</div>
    </li>

    @foreach ($user->travels as $travel)
        <li>
            <div class="collapsible-header">
                <span class="right">Gone: {{ $travel->leave_date }} - {{ $travel->return_date }}</span>
                {{ $travel->location }}
            </div>
            <div class="collapsible-body">
                <div class="row">
                    <div class="col s12 m4">Author: {{ $travel->author->userFullName }}</div>
                    <div class="col s12 m5">Created: {{ $travel->created_at->format('Y-m-d') }}</div>
                    <div class="col s12 m3">Updated: {{ $travel->updated_at->format('Y-m-d') }}</div>
                </div>
                <div class="row">
                    <div class="col s12 m4">Travel Brief Date: {{ $travel->brief_date }}</div>
                    <div class="col s12 m5">Travel Debrief Date: {{ $travel->debrief_date }}</div>
                </div>
                <div class="row">
                    <div class="col s4 m2">Comment: </div>
                    <div class="col s8 m10">{!! nl2br(e($travel->comment)) !!}</div>
                </div>
                @if($travel->attachments->count())
                    <div class="row">
                        <div class="col s4 m2">Attachments: </div>
                        <div class="col s8 m10">
                            @foreach($travel->attachments as $file)
                                {!! Form::open(array('action' => ['AttachmentController@destroy', $file->id], 'method' => 'DELETE', 'class' => 'form-inline')) !!}
                                <span class="chip">
                                        <a href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }}</a>
                                    @can('update_record', $travel)
                                        <button class="btn-flat btn-sm" type="submit">
                                                    <i class="material-icons">close</i>
                                                </button>
                                    @endcan
                                        </span> &nbsp;
                                {!! Form::close() !!}
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col s12 right-align">
                        @can('edit')<button type="submit" data-url="/user/{{ $user->id }}/travel/{{$travel->id}}" class="btn-flat delete-record blue-text" data-type="travel">Delete</button> @endcan
                        @can('edit')<a href="{{ action('TravelController@edit', [$user->id, $travel->id]) }}" class="btn-flat blue-text">Edit</a> @endcan
                    </div>
                </div>
            </div>
        </li>
    @endforeach
    @can('edit')
        <li>
            <div class="collapsible-footer right-align">
                <a class="btn modal-trigger" href="{{ action('TravelController@create', $user->id) }}">Add Travel</a>
            </div>
        </li>
    @endcan
</ul>
