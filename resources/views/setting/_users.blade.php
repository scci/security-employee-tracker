<div class="row">
    <div class="col s12 m8">

        <div class="row">
            <div class="col s12">
                {!! Form::label('admin[]', 'Admins:') !!}
                {!! Form::select('admin[]', $userList, $admins ,['multiple']) !!}
                @if($configAdmins)<small>From Config: {{ $configAdmins }}</small>@endif
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                {!! Form::label('viewer[]', 'View Only Access:') !!}
                {!! Form::select('viewer[]', $userList, $viewers ,['multiple']) !!}
            </div>
        </div>
    </div>
    <div class="col s12 m4">
        <div class="row">
            <div  class="input-field">
                <select class="validate" id="js-listOfAccessTokens">
                    <option selected disabled hidden></option>
                    @foreach ($accessTokens as $token)
                    <option value="{{$token->id}}">{{$token->name}}</option>
                    @endforeach
                </select>
                <label>Select Token to edit:</label>
            </div>
        </div>
            <div class="row" style="display:none" id="js-addNewAccessTokenName">
                {!! Form::label('newAccessToken', 'Create New Access Token:') !!}
                {!! Form::text('newAccessToken', null, array('placeholder' => 'Enter Name of New Access Token') ) !!}
            </div>
            <div class="row" style="display:none" id="js-editAccessTokenFormField">
                {!! Form::label('editAccessToken', 'Edit Access Token') !!}
                {!! Form::text('editAccessToken[name]', null) !!}
                <input type="hidden" id="editAccessTokenId" name="editAccessToken[id]">
            </div>
        <div class="row">
            <div class="col s5">
                <button  type="button" class="btn"  id="js-openFormFieldForAddNewAccessToken">New Token</button> 
            </div>
            <div class="col s5">
                <button  type="button" class="btn" style="display:none" id="js-showEditAccessTokenNameField">Edit Token Name</button> 
            </div>
        </div>
    </div>

</div>