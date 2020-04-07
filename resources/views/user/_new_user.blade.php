<!-- end add user button row fix -->

<div class="row">
    <div class="col s12 m6 l4">
        <div class="input-field" id="first_name_field">
            {!! Form::label('first_name', 'First Name:') !!}
            {!! Form::text('first_name', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="nickname_field">
            {!! Form::label('nickname', 'Nickname:') !!}
            {!! Form::text('nickname', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="last_name_field">
            {!! Form::label('last_name', 'Last Name:') !!}
            {!! Form::text('last_name', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="email_field">
            {!! Form::label('email', 'Email:') !!}
            {!! Form::text('email', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="username_field">
            {!! Form::label('username', 'Username:') !!}
            {!! Form::text('username', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="phone_field">
            {!! Form::label('phone', 'Phone:') !!}
            {!! Form::text('phone', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="emp_num_field">
            {!! Form::label('emp_num', 'Employee ID:') !!}
            {!! Form::text('emp_num', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="supervisor_field">
            {!! Form::select('supervisor_id', array(null=>'None') + $supervisors, null, ['class' => 'validate']) !!}
            {!! Form::label('supervisor_id', 'Supervisor:') !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="clearance_field">
            {!! Form::select('access_level', [null => 'None', 'S' => 'Secret', 'TS' => 'Top Secret'], null, ['class' => 'validate']) !!}
            {!! Form::label('access_level', 'Access Level:') !!}
        </div>
    </div>

    <div class="col s12 m6 l4">
        <div class="input-field" id="clearance_field">
            {!! Form::select('clearance', [null => 'None', 'Int S' => 'Interim Secret', 'S' => 'Secret', 'SCI' => 'SCI', 'Int TS' => 'Interim Top Secret', 'TS' => 'Top Secret'], null, ['class' => 'validate']) !!}
            {!! Form::label('clearance', 'JPAS Clearance:') !!}
        </div>
    </div>
    <div class="col s12 m6 l4" id="elig_date_field">
        {!! Form::label('elig_date', 'JPAS Eligibility Date:') !!}
        {!! Form::date('elig_date', null, ['class' => 'datepicker']) !!}
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="inv_field">
            {!! Form::label('inv', 'JPAS Investigation:') !!}
            {!! Form::text('inv', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4" id="inv_close_field">
        {!! Form::label('inv_close', 'JPAS Investigation Date:') !!}
        {!! Form::date('inv_close', null, ['class' => 'datepicker']) !!}
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="groups_field">
            <select name='groups[]' id="_new_user_groups_field" multiple>
                <option value="" disabled selected>Choose Group(s)</option>
                @foreach($groups as $group)
                    <option data-closed-area="{{ $group->closed_area }}" value="{{ $group->id }}"
                        @if($submit == 'Update' && $user->groups->where('id', $group->id)->first())
                            selected
                        @endif
                    >{{ $group->name }}</option>
                @endforeach
            </select>
            {!! Form::label('groups[]', 'Groups:') !!}
        </div>
    </div>
    @if($submit == 'Update')
        @foreach($groups as $group)
            @if($group->closed_area)
                <?php $userHasGroup = $user->groups->where('id', $group->id)->first(); ?>
                <div class="col s12 m6 l4 closed-area" id="access-{{ $group->id }}" style="@unless($userHasGroup)display:none; @endunless">
                    <div class="input-field">
                        {!! Form::select("access[$group->id]", [null => 'None', 'Unrestricted' => 'Unrestricted', 'Working Hours' => 'Working Hours'], ($userHasGroup ? $userHasGroup->pivot->access : null), ['class' => 'validate']) !!}
                        {!! Form::label("access[$group->id]", "$group->name Closed Area:") !!}
                    </div>
                </div>
            @endif
        @endforeach
    @endif
    <div class="col s12 m6 l4">
        <div class="input-field" id="status_field">
            {!! Form::select('status', ['active' => 'Active', 'separated' => 'Separated'], null, ['class' => 'validate']) !!}
            {!! Form::label('status', 'Status:') !!}
        </div>
    </div>
    <div class="col s12 m6 l4" id="separated_field">
        {!! Form::label('separated_date', 'Separated Date:') !!}
        {!! Form::date('separated_date', null, ['class' => 'datepicker']) !!}
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="cont_eval_field">
            {!! Form::select('cont_eval', [false => 'No', true => 'Yes'], null, ['class' => 'validate']) !!}
            {!! Form::label('cont_eval_label', 'Continuous Evaluation:') !!}
        </div>
    </div>
    <div class="col s12 m6 l4 hidden"  id="cont_eval_date_field">
        {!! Form::label('cont_eval_date_label', 'Continuous Evaluation Date:') !!}
        {!! Form::date('cont_eval_date', null, ['class' => 'datepicker']) !!}
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="cac_issued_field">
            {!! Form::select("accessTokens[cac_issued]", [0 => 'No',  1 => 'Yes', false => 'No',  true => 'Yes'], null, ['class' => 'validate']) !!}
            {!! Form::label('cac_issued_lable', 'CAC Issued:') !!}   
        </div> 
    </div>
    <div class="col s12 m6 l4 hidden"  id="cac_issue_date_field">
        {!! Form::label('cac_issue_date_lable', 'CAC Issue Date:') !!}
        {!! Form::date("accessTokens[cac_issue_date]", null, ['class' => 'datepicker']) !!}
    </div>
    <div class="col s12 m6 l4 hidden"  id="cac_expiration_date_field">
        {!! Form::label('cac_expiration_date_lable', 'CAC Expiration Date:') !!}
        {!! Form::date('accessTokens[cac_expiration_date]', null, ['class' => 'datepicker']) !!}
    </div>
    <div class="col s12 m6 l4 hidden"  id="cac_return_date_field">
        {!! Form::label('cac_return_date_lable', 'CAC Return Date:') !!}
        {!! Form::date('accessTokens[cac_return_date]', null, ['class' => 'datepicker']) !!}
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="sipr_issued_field">
            {!! Form::select('accessTokens[sipr_issued]', [0 => 'No',  1 => 'Yes', false => 'No', true => 'Yes'], null, ['class' => 'validate']) !!}
            {!! Form::label('sipr_issue_label', 'SIPR TOKEN Issued:') !!}      
        </div>
    </div>
    <div class="col s12 m6 l4 hidden"  id="sipr_issue_date_field">
        {!! Form::label('sipr_issue_date_lable', 'SIPR Issue Date:') !!}
        {!! Form::date('accessTokens[sipr_issue_date]', null, ['class' => 'datepicker']) !!}
    </div>
    <div class="col s12 m6 l4 hidden"  id="sipr_expiration_date_field">
        {!! Form::label('sipr_expiration_date_label', 'SIPR Expiration Date:') !!}
        {!! Form::date('accessTokens[sipr_expiration_date]', null, ['class' => 'datepicker']) !!}
    </div>
    <div class="col s12 m6 l4 hidden"  id="sipr_return_date_field">
        {!! Form::label('sipr_return_date_label', 'SIPR Return Date:') !!}
        {!! Form::date('accessTokens[sipr_return_date]', null, ['class' => 'datepicker']) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 right-align">
        {!! Form::submit($submit, array('class' => 'btn-flat waves-effect waves-indigo')) !!}
    </div>
</div>
