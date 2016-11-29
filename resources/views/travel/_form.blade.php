<div class="row">
    <div class="input-field col s12">
        {!! Form::label('location', 'Location:') !!}
        {!! Form::text('location', null) !!}
    </div>
</div>

<div class="row">
    <div class="col m3 s6">
        {!! Form::label('leave_date', 'Leave date:') !!}
        {!! Form::date('leave_date', null, array('class' => 'datepicker')) !!}
    </div>
    <div class="col m3 s6">
        {!! Form::label('return_date', 'Return date:') !!}
        {!! Form::date('return_date', null, array('class' => 'datepicker')) !!}
    </div>
    <div class="col m3 s6">
        {!! Form::label('brief_date', 'Travel Brief date:') !!}
        {!! Form::date('brief_date', null, array('class' => 'datepicker')) !!}
    </div>
    <div class="col m3 s6">
        {!! Form::label('debrief_date', 'Travel Debrief date:') !!}
        {!! Form::date('debrief_date', null, array('class' => 'datepicker')) !!}
    </div>
</div>

<div class="row">
    <div class="col m8 s12" id="file_upload">
        <div class="clearfix bottom-buffer">
            {!! Form::multipleFiles() !!}
        </div>
    </div>
    <div class="col m4 s12">
        <p style="margin-top: 2em">
            {!! Form::hidden('encrypt', false) !!}
            <input type="checkbox" name="encrypt" value="true" class="filled-in" id="encrypt" />
            <label for="encrypt">File has PII/Encrypt File.</label>
        </p>
    </div>
    @if (isset($travel))
        <div class="col s12">Attachments:
            @foreach($travel->attachments as $file)
                <span class="chip">
                    <a href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }}</a>
                    <i class="material-icons close" data-id="{{$file->id}}">close</i>
                </span> &nbsp;
            @endforeach
        </div>
    @endif
</div>

<div class="row">
    <div class="input-field col s12">
        {!! Form::label('comment', 'Comment:') !!}<br />
        {!! Form::textarea('comment', null, array('class' => 'materialize-textarea', 'rows' => '4')) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 right-align">
        {!! Form::submit($submit, array('class' => 'btn-flat waves-effect waves-indigo')) !!}
    </div>
</div>
