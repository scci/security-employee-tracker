@foreach ($notes as $note)
    @if ($note->alert == $alert)
        @unless($note->private == 1 && $note->author_id != Auth::user()->id)
        <li>
            <div class="collapsible-header @if($alert) red lighten-2 @endif ">
                <div class="right">Modified: {{ $note->updated_at->format('Y-m-d') }}</div>
                @if($note->private == 1)
                    <i class="material-icons">visibility_off</i>
                @endif
                {{ $note->title }}

            </div>
            <div class="collapsible-body">
                <div class="row">
                    <div class="col s12 m2">
                        Author:
                    </div>
                    <div class="col s12 m10">
                        {{ $note->author->userFullName }}
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m2">
                        Comment:
                    </div>
                    <div class="col s12 m10">
                        {!! nl2br(e($note->comment)) !!}
                    </div>
                </div>
                @if($note->attachments->count())
                    <div class="row">
                        <div class="col s12 m3">Attachments: </div>
                        <div class="col s12 m9">
                            @foreach($note->attachments as $file)
                                {!! Form::open(array('action' => ['AttachmentController@destroy', $file->id], 'method' => 'DELETE', 'class' => 'form-inline')) !!}
                                <span class="chip">
                                    <a href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }}</a>
                                    @can('update_note', $note)
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
                        @can('edit')<button type="submit" data-url="/user/{{ $user->id }}/note/{{$note->id}}" class="btn-flat delete-record blue-text" data-type="note">Delete</button> @endcan
                        @can('edit')<a href="{{  action('NoteController@edit', [$user->id, $note->id]) }}" class="btn-flat blue-text">Edit</a> @endcan
                    </div>
                </div>
            </div>
        </li>
        @endunless
    @endif
@endforeach
