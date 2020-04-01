@if($user->accessTokens->cac_issued)
    <div class="divider" ></div>
    <div>
        <strong>CAC Issue Date:</strong>
        {{ $user->accessTokens->cac_issue_date }}
    </div>
    <div>
        <strong>CAC Expiration Date:</strong>
        {{ $user->accessTokens->cac_expiration_date }}
    </div>
    <div>
        <strong>CAC Return Date:</strong>
        {{ $user->accessTokens->cac_return_date == '0000-00-00' ? '' : $user->accessTokens->cac_return_date }}
    </div>
@endif
@if($user->accessTokens->sipr_issued && !$user->accessTokens->cac_issued)
    <div class="divider" ></div>
@endif
@if($user->accessTokens->sipr_issued)
    <div>
        <strong>SIPR TOKEN Issued Date:</strong>
        {{ $user->accessTokens->sipr_issue_date }}
    </div>
    <div>
        <strong>SIPR TOKEN Expiration Date:</strong>
        {{ $user->accessTokens->sipr_expiration_date }}
    </div>
    <div>
        <strong>SIPR TOKEN Return Date:</strong>
            {{ $user->accessTokens->sipr_return_date == '0000-00-00' ? '' : $user->accessTokens->sipr_return_date }}
    </div>
@endif