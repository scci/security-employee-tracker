<div class="row">
    <div class="col s12 m6 input-field">
        {!! Form::label('name', 'Name:') !!}
        {!! Form::text('name', null) !!}
    </div>
    <div class="col s12 m6 input-field">
        {!! Form::select('cycle', ['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'], 'weekly') !!}
        {!! Form::label('cycle', 'Cycle:') !!}
    </div>
    <div class="col s12 m3 switch" id="has_groups">
        Changes each cycle: <br /><br />
        <label>
            Individuals
            <input @if($submit == 'Update') disabled @endif type="checkbox" name="has_groups" value=1
			@if($submit == 'Update' && $duty->has_groups) checked @endif>
            <span class="lever"></span>
            Groups
        </label>
    </div>
    <div class="col s12 m9 input-field" id="duty_groups" style="display:none;">
        <select name='groups[]' multiple>
            <option value="" disabled selected>Choose Group(s)</option>
            @foreach($groups as $value => $group)
                <option value="{{ $value }}"
				@if($submit == 'Update' && $duty->groups->where('id', $value)->first())
                    selected
                @endif>{{ $group }}</option>
            @endforeach
        </select>
        {!! Form::label('groups[]', 'Groups:') !!}
    </div>
    <div class="col s12 m9 input-field" id="duty_users">
        <select name='users[]' multiple class="users-select">
            <option value="" disabled selected>Choose User(s)</option>
            @foreach($users as $value => $user)
                <option value="{{ $value }}"
				@if($submit == 'Update' && $duty->users->where('id', $value)->first())
                    selected
                @endif
				>{{ $user }}</option>
				
            @endforeach
        </select>
        {!! Form::label('users[]', 'Users:') !!}
    </div>
    <div class="col s12 input-field">
        {!! Form::textarea('description', null, ['class' => 'materialize-textarea']) !!}
        {!! Form::label('description', 'Description:') !!}
    </div>
</div>
<div class="row">
    <div class="col s12 right-align">
        {!! Form::reset('Reset', array('class' => 'btn-flat waves-effect waves-indigo', 'id' => 'training-reset')) !!}
        {!! Form::submit($submit, array('class' => 'btn-flat waves-effect waves-indigo')) !!}
    </div>
</div>

<script>
    $(function(){
        var has_groups = $('input[name="has_groups"]');
        if( has_groups.is(':checked')) {
            $('#duty_users').toggle();
            $('#duty_groups').toggle();
        }
        has_groups.change(function(){
            $('#duty_users').toggle();
            $('#duty_groups').toggle();
        });
    });
</script>
