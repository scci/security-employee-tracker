<div class="row">
    <div class="input-field col s12" id="training_select">
        {!! Form::select('training_id', [null => 'Pick a Training'] + $training, null, array($disabled)) !!}
        {!! Form::label('training_id', 'Training:') !!}
    </div>
</div>
<div class="row">
    <div class="col m6 s12" id="completed_date_field">
        {!! Form::label('completed_date', 'Completed date:') !!}
        {!! Form::date('completed_date', null, array('class' => 'datepicker')) !!}
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
</div>
@if ($trainingUser)
<div class="row">
    @foreach($trainingUser->attachments as $file)
        <span class="chip">
            <a href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }}</a>
            @can('update_record', $trainingUser)
                <i class="material-icons close" data-id="{{$file->id}}">close</i>
            @endcan
        </span> &nbsp;
    @endforeach
</div>
@endif
<div class="row">
    <div class="col s12 input-field" id="training_textarea">
        {!! Form::label('comment', 'User Notes:') !!}
        {!! Form::textarea('comment', null, array('class' => 'materialize-textarea')) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 right-align">
        {!! Form::reset('Reset', array('class' => 'btn-flat waves-effect waves-indigo', 'id' => 'training-reset')) !!}
        {!! Form::submit($submit, array('class' => 'btn-flat waves-effect waves-indigo')) !!}
    </div>
</div>
