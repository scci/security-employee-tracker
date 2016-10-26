@extends('layouts.master')

@section('title', 'JPAS Import')

@section('content')
    <h2>JPAS Import</h2>
    {!! Form::open(array('action' => 'UserController@resolveImport', 'method' => 'POST', 'files' => true, 'class' => 'form-inline note-form')) !!}

    {!! Form::hidden('uploadedFile', $uploadedFile ) !!}
    {!! Form::hidden('resolveImport', 1) !!}

    <div class="row">
        <div class="col m4">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">New Users</span>
                    @foreach($unique as $excelUser)
                        <div class="row">
                            <div class="input-field">
                            {!! Form::select($excelUser->name, array(null=>'Ignore this record.') + $userList, null) !!}
                            {!! Form::label($excelUser->name, $excelUser->name ) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col m8">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Update Users</span>
                    <table class="bordered">
                        <tr>
                            <th>User</th>
                            <th>Column</th>
                            <th>Old Record</th>
                            <th>New Record</th>
                            <th>Change</th>
                        </tr>

                        @foreach($changes as $row)
                            <tr>
                                <td>
                                    {{ $row['user']->last_name }}, {{ $row['user']->first_name }}
                                </td>
                                <td>
                                    {{ $row['field'] }}
                                </td>
                                <td>
                                    {{ $row['original'] }}
                                </td>
                                <td>
                                    {{ $row['new'] }}
                                </td>
                                <td>
                                    <input type="hidden" name="approve[{{$row['user']->id}}][{{$row['field']}}]" value="0" />
                                    <input type="checkbox" name="approve[{{ $row['user']->id}}][{{$row['field']}}]" value="{{$row['new']}}" class="filled-in" id="approve[{{ $row['user']->id}}][{{$row['field']}}]" checked />
                                    <label for="approve[{{ $row['user']->id}}][{{$row['field']}}]">Approve</label>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col s12 right-align">
            {!! Form::reset('Reset', array('class' => 'btn-flat waves-effect waves-indigo', 'id' => 'training-reset')) !!}
            {!! Form::submit('Import', array('class' => 'btn-flat waves-effect waves-indigo')) !!}
        </div>
    </div>

    {!! Form::close() !!}
@stop

@section('help')
    <p><strong>New Users</strong></p>
    <p>If a JPAS user was not mapped to an application user, then you will see a select form with the JPAS user as the label and a list of users to select. Simply pick the user listed in the application to connect them.</p>
    <p>Once a JPAS user is mapped to an application user, all future JPAS imports for the user will be listed under the "Update Users" section.</p>

    <p><strong>Update Users</strong></p>
    <p>When a user record changes due to a JPAS import, you are able to approve or reject the changes. By default all changes are approved, however simply unchecking the approve checkbox will ignore that change.</p>
    <p>Example reason not to approve: You have manually editted a JPAS value and wish to keep the value you set over what is listed in JPAS.</p>

    <p><strong>Performing the Import</strong></p>
    <p>Once you have set the update and new users sections, click import and the JPAS data will be imported and you will be directed to the home page.</p>
    <p>The home page will list all changes that were preformed due to the JPAS import which you may review.</p>
@stop