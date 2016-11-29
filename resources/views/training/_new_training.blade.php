<div class="row">
    <div class="col s12 m6">
        <div class="input-field" id="training_name">
            {!! Form::label('name', 'Name:') !!}
            {!! Form::text('name', null) !!}
        </div>
    </div>
    <div class="col s12 m3">
        <div class="input-field" id="training_renews_in">
            {!! Form::label('renews_in', 'Days until Renewal:') !!}
            {!! Form::text('renews_in', null, ['class' => 'active']) !!}
        </div>
    </div>
    <div class="col s12 m3">
        <div class="input-field" id="administrative_training">
            {!! Form::hidden('administrative', false) !!}
            {!! Form::checkbox('administrative', 1, null, ['id' => 'administrative', 'class' => 'filled-in']) !!}
            {!! Form::label('administrative', 'Administrative') !!}
        </div>
    </div>
    <div class="input-field col s12" id="training_description">
        {{-- Form::label('description', 'Instructions:', ['class' => 'active']) --}}
        {!! Form::textarea('description', null, ['class' => 'wysiwyg']) !!}
    </div>
    <div class="col s12" id="file_upload">
        {!! Form::multipleFiles() !!}
        @if (isset($training))
            Attachments:
            @foreach($training->attachments as $file)
                <span class="chip">
                    <a href="{{ url('/attachment', $file->id) }}" alt="{{ $file->filename }}">{{ $file->filename }}</a>
                    <i class="material-icons close" data-id="{{$file->id}}">close</i>
                </span> &nbsp;
            @endforeach
        @endif
    </div>
    <div class="col s12 m6">
        <div class="input-field" id="assignment_buttons">
            <div class="">Assignment:</div>
            {!! Form::radio('assign', 'None', true,  ['id' => 'ab-none']) !!}
            {!! Form::label('ab-none', 'None ') !!}
            {!! Form::radio('assign', 'due_date', false,  ['id' => 'ab-due-date']) !!}
            {!! Form::label('ab-due-date', 'Due Date ') !!}
        </div>
    </div>
    <div class="col s12 m6" id="training_due_date">
        {!! Form::label('due_date', 'Due Date:') !!}
        {!! Form::date('due_date', null, ['class' => 'datepicker']) !!}
    </div>
    <div class="col s12 m6 input-field" id="group_field">
        {!! Form::selectBox('Group', 'groups[]', $groups, true) !!}
        {!! Form::label('groups[]', 'Groups:') !!}
    </div>
    <div class="col s12 m6 input-field" id="user_field">
        {!! Form::selectBox('User', 'users[]', $users, true) !!}
        {!! Form::label('users[]', 'Users:') !!}
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
