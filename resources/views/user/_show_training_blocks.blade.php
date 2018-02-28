<ul class="collapsible popout" data-collapsible="accordion">
    <li>
        @if(ucfirst($sectionId) == "Scheduled" || ucfirst($sectionId == ""))
        <div class="collapsible-title"><h5>Training Due</h5></div>        
        @else
            <div class="collapsible-title">{{ucfirst($sectionId)}}</div>
        @endif
    </li>
    @foreach ($trainings as $trainingUser)
        @if (!$trainingUser->training->administrative
            && $training_user_types[$trainingUser->id] == $sectionId)
            <li>
                <div class="collapsible-header" >
                    <div class="right">
                        @if($trainingUser->completed_date)
                            Completed: {{ $trainingUser->completed_date }}
                        @elseif($trainingUser->due_date)
                            Due Date: {{ $trainingUser->due_date }}
                            <a type="button" class="btn-flat btn-sm tooltipped" href="{{url('training/reminder', $trainingUser->id)}}" data-position="top" data-tooltip="Send Reminder">
                                <i class="material-icons training-user-icon">email</i>
                            </a>
                        @endif
                    </div>
                    @if(!$trainingUser->completed_date
                        && \Carbon\Carbon::today() > \Carbon\Carbon::createFromFormat('Y-m-d', $trainingUser->due_date))
                        <span class="tooltipped" data-tooltip="Past Due">
                            <i class="material-icons red-text">warning</i>
                        </span>
                    @endif
                    @if($trainingUser->stop_renewal)
                        <span class="tooltipped" data-tooltip="Don't Auto-renew">
                            <i class="material-icons">sync_disabled</i>
                        </span>
                    @endif
                    {{ $trainingUser->training->name }}
                </div>

                <div class="collapsible-body">
                    <div class="row">
                        <div class="col s12 m4">Assigned: {{ $trainingUser->created_at }}</div>
                        <div class="col m4 center-align">Due Date: {{ $trainingUser->due_date }}</div>
                        <div class="col m4 right-align">Completed: {{ $trainingUser->completed_date }}</div>
                    </div>
                    <div class="row">
                        <div class="col m3">Training Instructions:</div>
                        <div class="col m9 browser-default">
                            {!! Markdown::convertToHTML($trainingUser->training->description) !!} <br />
                            @foreach($trainingUser->training->attachments as $file)
                                @if(!$file->admin_only)
                                    <div class="chip">
                                        <a href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }}</a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="row">
                        <div class="col m3">User Notes:</div>
                        <div class="col m9">
                            {!! nl2br(e($trainingUser->comment)) !!} <br />
                            @foreach($trainingUser->attachments as $file)
                                <span class="chip">
                                        <a href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }}</a>
                                    @can('update_record', $trainingUser)
                                        <i class="material-icons delete-attachment" data-id="{{$file->id}}">close</i>
                                    @endcan
                                </span> &nbsp;
                            @endforeach
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 m6">
                            @can('view')<a href="{{ url('/training', $trainingUser->training_id) }}">View Training Page</a>@endcan
                            &nbsp;
                        </div>
                        <div class="col s12 m6 right-align">
                            @can('edit')<button type="submit" data-url="/user/{{ $user->id }}/training/{{$trainingUser->id}}" class="btn-flat delete-record blue-text" data-type="training">Delete</button> @endcan
                            @can('update_record', $trainingUser) <a href="{{  action('TrainingUserController@edit', [$user->id, $trainingUser->id]) }}" class="btn-flat blue-text">Edit</a> @endcan
                        </div>
                    </div>
                </div>
            </li>
        @endif
    @endforeach
    <li>
        <div class="collapsible-footer left-align">
            @if(ucfirst($sectionId != "") && ucfirst($sectionId) != "Scheduled")                
                <a class="right btn" href="{{ action('UserController@show', $user->id) }}/{{$sectionId}}/show">Show All</a>
            @else 
                @can('edit')        
                    <a class="right btn" href="{{ action('TrainingUserController@create', $user->id) }}">Add Training</a>        
                @endcan
            @endif
        </div>    
    </li>
</ul>
