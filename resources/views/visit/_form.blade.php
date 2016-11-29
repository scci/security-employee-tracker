<div class="row">
    <div class="col s12 input-field">
        {!! Form::label('smo_code', 'SMO Code:') !!}
        {!! Form::text('smo_code', null) !!}
    </div>
</div>

<div class="row">
    <div class="col m6 s12">
        {!! Form::label('visit_date', 'Visit date:') !!}
        {!! Form::date('visit_date', null, array('class' => 'datepicker')) !!}
    </div>
    <div class="col m6 s12">
        {!! Form::label('expiration_date', 'Expiration date:') !!}
        {!! Form::date('expiration_date', null, array('class' => 'datepicker')) !!}
    </div>
</div>

<div class="row">
    <div class="col m6 s12 input-field">
        {!! Form::label('poc', 'Point of Contact:') !!}
        {!! Form::text('poc', null) !!}
    </div>
    <div class="col m6 s12 input-field">
        {!! Form::label('phone', 'PoC Phone Number:') !!}
        {!! Form::text('phone', null) !!}
    </div>
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
