<div class="row">
    <div class="input-field col m8 s12" id="training_select">
        {!! Form::select('training_id', [null => 'Pick a Training'] + $training, null, array($disabled)) !!}
        {!! Form::label('training_id', 'Training:') !!}
    </div>
    @can('edit')
        <div class="col m4 s12" id="stop_renewal_field">
            <p style="margin-top: 2em">
                {!! Form::hidden('stop_renewal', false) !!}
                {!! Form::checkbox('stop_renewal', 1, null, ['id' => 'stop_renewal', 'class' => 'filled-in']) !!}
                {!! Form::label('stop_renewal', 'Don\'t Auto-renew') !!}
            </p>
        </div>
    @endcan('edit')
</div>
<div class="row">
    <div class="col m6 s12" id="completed_date_field">
        {!! Form::label('completed_date', 'Completed date:') !!}
        {!! Form::date('completed_date', $submit == 'Update' ? ($trainingUser->completed_date ?? \Carbon\Carbon::today()) : null, array('class' => 'datepicker')) !!}
    </div>
    <div class="col m6 s12" id="due_date_field">
        {!! Form::label('due_date', 'Due date:') !!}
        {!! Form::date('due_date', null, array('class' => 'datepicker', $disabled )) !!}
    </div>
</div>
<div class="row">
    <div class="col m8 s12" id="file_upload">
        {!! Form::multipleFiles() !!}
    </div>
    <div class="col m4 s12">
        <p style="margin-top: 2em">
            {!! Form::hidden('encrypt', false) !!}
            <input type="checkbox" name="encrypt" value="true" class="filled-in" id="encrypt" />
            <label for="encrypt">File has PII/Encrypt File.</label>
        </p>
    </div>
    @if (isset($trainingUser))
        <div class="col s12">Attachments:
            @foreach($trainingUser->attachments as $file)
                <span class="chip">
                    <a href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }}</a>
                    <i class="material-icons delete-attachment" data-id="{{$file->id}}">close</i>
                </span> &nbsp;
            @endforeach
        </div>
    @endif
</div>

<div class="row">
    <div class="col s12 input-field" id="training_textarea">
        {!! Form::label('comment', 'User Notes:') !!}
        {!! Form::textarea('comment', $trainingUser->comment ?? '', array('class' => 'materialize-textarea', $disabled)) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 right-align">
        {!! Form::submit($submit, array('class' => 'btn-flat waves-effect waves-indigo')) !!}
    </div>
</div>
