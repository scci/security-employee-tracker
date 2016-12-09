<div class="row">
    <div class="col s12 m6 l4">
        {!! Form::label('mail.driver', 'Mail Driver') !!}
        {!! Form::select('mail.driver', ['mail' => 'PHP Mail', 'sendmail'=> 'Sendmail', 'smtp' => 'SMTP', 'log' => 'System Logs'], $settings['mail.driver'] ?? config('mail.driver')) !!}
    </div>
</div>

<div class="row">
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('mail.host', 'SMTP Host:') !!}
        {!! Form::text('mail.host', $settings['mail.host'] ?? config('mail.host')) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('mail.username', 'SMTP Username:') !!}
        {!! Form::text('mail.username', $settings['mail.username'] ?? config('mail.username')) !!}
    </div>
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('mail.password', 'SMTP Password:') !!}
        <input type="password" name="mail.password" />
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('mail.port', 'SMTP Port:') !!}
        {!! Form::text('mail.port', $settings['mail.port'] ?? config('mail.port')) !!}
    </div>
    <div class="col s12 m6 l4 input-field">
        {!! Form::label('mail.encryption', 'SMTP Encryption Protocol:') !!}
        {!! Form::text('mail.encryption', $settings['mail.encryption'] ?? config('mail.encryption')) !!}
    </div>
</div>
