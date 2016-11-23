@if ($trainings->each(function ($trainingUser) {
    if ($trainingUser->training->administrative){
        return true;
    }
}))
    <ul class="collapsible" data-collapsible="accordion">
        <li><div class="collapsible-title">Administrative</div></li>
        @foreach ($trainings as $trainingUser)
            @if ($trainingUser->training->administrative)
                <li>
                    <div class="collapsible-header" >
                        {{ $trainingUser->training->name }} <small>{{ $trainingUser->completed_date }}</small>
                    </div>

                    <div class="collapsible-body">
                        <div class="row">
                            <div class="col s12">Due Date: {{ $trainingUser->due_date }}</div>
                            <div class="col s12">Completed: {{ $trainingUser->completed_date }}</div>
                        </div>
                        <div class="row">
                            <div class="col s12">Training Instructions:</div>
                            <div class="col s12 browser-default">
                                {!! Markdown::convertToHTML($trainingUser->training->description) !!} <br />
                                @foreach($trainingUser->training->attachments as $file)
                                    <div class="chip">
                                        <a href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }}</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12">User Notes:</div>
                            <div class="col s12">
                                {!! nl2br(e($trainingUser->comment)) !!} <br />
                                @foreach($trainingUser->attachments as $file)
                                    <span class="chip">
                                            <a href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }}</a>
                                        @can('update_record', $trainingUser)
                                            <i class="material-icons close" data-id="{{$file->id}}">close</i>
                                        @endcan
                                    </span> &nbsp;
                                @endforeach
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12 right-align">
                                @can('edit')<button type="submit" data-url="/user/{{ $user->id }}/training/{{$trainingUser->id}}" class="btn-flat delete-record blue-text" data-type="training">Delete</button> @endcan
                                @can('update_record', $trainingUser) <a href="{{  action('TrainingUserController@edit', [$user->id, $trainingUser->id]) }}" class="btn-flat blue-text">Edit</a> @endcan
                            </div>
                        </div>
                    </div>
                </li>
            @endif
        @endforeach
    </ul>
@endif
