<div class="row">
    <div class="col s12 m6">
        <div class="input-field" id="trainingtype_name">
            {!! Form::label('name', 'Name:') !!}
            {!! Form::text('name', null) !!}
        </div>
    </div>
    <div class="col s12 m6">
        <div class="input-field" id="status_buttons">
            <div class="">Status:</div>
            {!! Form::radio('status', '1', true,  ['id' => 'ab-active']) !!}
            {!! Form::label('ab-active', 'Active ') !!}
            {!! Form::radio('status', '0', false,  ['id' => 'ab-inactive']) !!}
            {!! Form::label('ab-inactive', 'Inactive ') !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="input-field col s12" id="trainingtype_description">
        Description:
        {!! Form::textarea('description', null, ['class' => 'wysiwyg']) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 right-align">
        {!! Form::submit($submit, array('class' => 'btn-flat waves-effect waves-indigo')) !!}
    </div>
</div>

<script>
    new SimpleMDE({spellChecker: false});
</script>
