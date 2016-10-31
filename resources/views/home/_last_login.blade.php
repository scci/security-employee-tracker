<div class="card-panel blue-grey lighten-4 center-align" role="alert">
    <div>Welcome Back {{ $logged_in_user->userFullName }}</div>
    <div>You last logged on {{ Session::get('last_logon') }} from {{ Session::get('ip') }}.</div>
</div>
