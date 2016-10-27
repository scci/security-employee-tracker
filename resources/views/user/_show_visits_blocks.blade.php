<ul class="collapsible popout" data-collapsible="accordion">
    <li>
        <div class="collapsible-title">Visits</div>
    </li>

    @foreach ($visits as $visit)
        <?php $diffInWeeks = \Carbon\Carbon::createFromFormat('Y-m-d', $visit->expiration_date)->diffinWeeks(\Carbon\Carbon::today(), false); ?>
        @if( $diffInWeeks <= 1)
        <li>
            <div class="collapsible-header">
                <span class="right">Expires: {{ $visit->expiration_date }} </span>
                <div class="col s6 m3">{{ $visit->smo_code }} </div>
            </div>
            <div class="collapsible-body">
                <div class="row">
                    <div class="col s4 m2">Visit: </div>
                    <div class="col s8 m10">{{ $visit->visit_date }}</div>
                </div>
                <div class="row">
                    <div class="col s4 m2">POC: </div>
                    <div class="col s8 m10">{{ $visit->poc }} &nbsp; {{ $visit->phone }}</div>
                </div>
                <div class="row">
                    <div class="col s4 m2">Comment: </div>
                    <div class="col s8 m10">{{ $visit->comment }}</div>
                </div>
                <div class="row">
                    <div class="col s12 right-align">
                        @can('edit')<button type="submit" data-url="/user/{{ $user->id }}/visit/{{$visit->id}}" class="btn-flat delete-record blue-text" data-type="visit">Delete</button> @endcan
                        @can('edit')<a href="{{  action('VisitController@edit', [$user->id, $visit->id]) }}" class="btn-flat blue-text">Edit</a> @endcan
                    </div>
                </div>
            </div>
        </li>
        @endif
    @endforeach
    @can('edit')
        <li>
            <div class="collapsible-footer right-align">
                <a class="btn modal-trigger" href="{{ action('VisitController@create', $user->id) }}">Add Visit</a>
            </div>
        </li>
    @endcan
</ul>
