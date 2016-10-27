<div class="row">
    <div class="col s12 input-field">
        {!! Form::label('title', 'Title:') !!}
        {!! Form::text('title', null) !!}
    </div>
</div>
<div class="row">
    <div class="col m6 s12" id="private_checkbox">
        {!! Form::hidden('private', false) !!}
        {!! Form::checkbox('private', 1, null, ['id' => 'private', 'class' => 'filled-in']) !!}
        {!! Form::label('private', 'Private') !!}
    </div>
    <div class="col m6 s12" id="alert_checkbox">
        {!! Form::hidden('alert', false) !!}
        {!! Form::checkbox('alert', 1, null, ['id' => 'alert', 'class' => 'filled-in']) !!}
        {!! Form::label('alert', 'Alert') !!}
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
</div>
<div class="row">
    <div class="col s12 input-field">
        {!! Form::label('comment', 'Description:') !!}
        {!! Form::textarea('comment', null, array('class' => 'materialize-textarea')) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 right-align">
        {!! Form::reset('Reset', array('class' => 'btn-flat waves-effect waves-indigo', 'id' => 'training-reset')) !!}
        {!! Form::submit($submit, array('class' => 'btn-flat waves-effect waves-indigo')) !!}
    </div>
</div>
