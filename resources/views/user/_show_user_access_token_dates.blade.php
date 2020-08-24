@foreach ($user->sortedUserAccessTokenCollection() as $token)
    <div class="divider"></div>
    <div>
        <strong>{{$token->accessToken->name}} Issue Date:</strong>
        {{ $token->token_issue_date }}
    </div>
    <div>
        <strong>{{$token->accessToken->name}} Expiration Date:</strong>
        {{ $token->token_expiration_date }}
    </div>
    <div>
        <strong>{{$token->accessToken->name}} Return Date:</strong>
        {{ $token->token_return_date}}
    </div>
@endforeach