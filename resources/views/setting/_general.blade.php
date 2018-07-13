<div class="row">
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('app_name', 'Application Name:') !!}
        {!! Form::text('app_name', $settings['app_name'] ?? 'SET') !!}
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('mail.from.name', 'Send Emails As:') !!}
        {!! Form::text('mail.from.name', $settings['mail.from.name'] ?? config('mail.from.name')) !!}
    </div>
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('mail.from.address', 'Send Emails From:') !!}
        {!! Form::text('mail.from.address', $settings['mail.from.address'] ?? config('mail.from.address')) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 m6">
        {!! Form::label('summary_recipient', 'Summary Email Recipient:') !!}
        {!! Form::select('summary_recipient[]', $userList, $settings['summary_recipient'] ,['multiple']) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l4">
        {!! Form::label('full_name_format', 'Full Name Format:') !!}
        {!! Form::select('full_name_format', ['last_first' => 'LastName, FirstName (nickname)', 'first_last'=> 'FirstName (nickname) LastName'], $settings['full_name_format'] ?? 'last_first') !!}
    </div>
</div>