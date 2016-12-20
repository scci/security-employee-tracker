{!! Form::hidden('encrypt', false) !!}

<div class="row">
    <div class="col s12">
        <strong>Due Date:</strong> {{ $trainingUser->due_date }}
    </div>
</div>
<div class="row">
    <div class="col m3 s12" id="completed_date_field">
        {!! Form::label('completed_date', 'Completed date:') !!}
        {!! Form::date('completed_date', \Carbon\Carbon::today(), array('class' => 'datepicker')) !!}
    </div>

    <div class="col offset-m2 m7 s12" id="file_upload">
        {!! Form::multipleFiles() !!}
        @if($trainingUser->attachments->count())
            Attachments:
            @foreach($trainingUser->attachments as $file)
                <span class="chip">
                    <a href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }}</a>
                    <i class="material-icons delete-attachment" data-id="{{$file->id}}">close</i>
                </span> &nbsp;
            @endforeach
        @endif
    </div>
</div>
<div class="row">
    <div class="col s12">
        {!! Form::submit('Save and return to profile', array('class' => 'btn-flat right')) !!}
    </div>
</div>

{!! Form::close() !!}
