<div class="row">
    <div class="col s12 m6">
        {!! Form::label('admin[]', 'Admins:') !!}
        {!! Form::select('admin[]', $userList, $admins ,['multiple']) !!}
        @if($configAdmins)<small>From Config: {{ $configAdmins }}</small>@endif
    </div>
</div>
<div class="row">
    <div class="col s12 m6">
        {!! Form::label('viewer[]', 'View Only Access:') !!}
        {!! Form::select('viewer[]', $userList, $viewers ,['multiple']) !!}
    </div>
</div>