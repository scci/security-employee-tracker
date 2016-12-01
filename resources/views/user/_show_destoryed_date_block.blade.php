  <div class="row">
    <div class="col s12">
      <div class="card-panel  orange darken-2">
        <span class="white-text">The user {{ $user->first_name }} {{ $user->last_name}} is scheduled to be
          destroyed on {{ $user->destroyed_date->format('Y-m-d') }}.
        </span>
      </div>
    </div>
  </div>
