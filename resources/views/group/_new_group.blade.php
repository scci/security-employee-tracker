
<div class="row">
    <div class="input-field col m6 s12" id="training_name">
        {!! Form::label('name', 'Name:') !!}
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
    <div class="col m6 s12">
        <p style="margin-top: 2em">
            {!! Form::hidden('closed_area', 0) !!}
            <input type="checkbox" name="closed_area" value=1 class="filled-in" id="closed_area" @if($submit == 'Update' && $group->closed_area) checked @endif />
            <label for="closed_area">Has closed area</label>
        </p>
    </div>
</div>
<div class="row">
    <div class="col s12 m6 input-field" id="user_field">
        {!! Form::select('users[]', [null => 'None'] + $users, $selectedUsers, ['multiple']) !!}
        {!! Form::label('users[]', 'Users:') !!}
    </div>
    <div class="input-field col s12 m6" id="training_field">
        {!! Form::select('trainings[]', [null => 'None'] + $training, $selectedTraining, ['multiple']) !!}
        {!! Form::label('trainings[]', 'Training Subscriptions:') !!}
    </div>
</div>
<div class="row">
    <div class="col s12 right-align">
        {!! Form::submit($submit, array('class' => 'btn-flat waves-effect waves-indigo')) !!}
    </div>
</div>
