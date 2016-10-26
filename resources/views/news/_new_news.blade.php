<div class="row">
    <div class="input-field col s12" id="news_title">
       {!! Form::label('title', 'Title:') !!}
       {!! Form::text('title', null) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 m6" id="publish_date">        
        {!! Form::label('publish_date', 'Publish Date:') !!}
        {!! Form::date('publish_date', $submit == 'Update' ? null : Carbon\Carbon::today(), ['class' => 'datepicker']) !!}
    </div>
    <div class="col s12 m6" id="expire_date">        
        {!! Form::label('expire_date', 'Expires On:') !!}
        {!! Form::date('expire_date', null, ['class' => 'datepicker']) !!}
    </div>
</div>
<div class="input-field col s12" id="news_description">
    {!! Form::label('description', 'Description:') !!}<br />
    {!! Form::textarea('description', null, ['class' => 'wysiwyg']) !!}
</div>
<div class="col s12" id="file_upload">
    <div class="file-field input-field">
        <div class="btn">
            <span>File</span>
            {!! Form::file('files[]', array('multiple' => true)) !!}
        </div>
        <div class="file-path-wrapper">
            <input class="file-path validate" type="text" placeholder="Upload one or more files">
        </div>
    </div>
</div>

<div class="row">
    <div class="col m4 s12">
        <p style="margin-top: 2em">
            {!! Form::hidden('send_email', 0) !!}
            <input type="checkbox" name="send_email" value=1 class="filled-in" checked id="send_email" />
            <label for="send_email">Email news on publish date</label>
        </p>
    </div>    
</div>

<div class="row">
    <div class="col s12 right-align">
        {!! Form::reset('Reset', array('class' => 'btn-flat waves-effect waves-indigo', 'id' => 'training-reset')) !!}
        {!! Form::submit($submit, array('class' => 'btn-flat waves-effect waves-indigo')) !!}
    </div>
</div>