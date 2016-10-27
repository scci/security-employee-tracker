<div id="sidebar-action-items" class="side-nav fixed">

    <nav>
        <form class="typeahead" role="search">
            <div class="input-field">
                <input id="search" type="search" name="q" autocomplete="off" class="search-input">
                <label for="search"><i class="material-icons">search</i></label>
                {{--<i class="material-icons">close</i>--}}
            </div>
        </form>
    </nav>

    <ul class="collapsible" data-collapsible="expandable">
    @if ($eligibilityRenewal->count())
        <li>
            <div class="collapsible-header active">Eligibility Renewal</div>
            <div class="collapsible-body row">
                <div class="col s9 small">
                    Employee
                </div>
                <div class="col s3 small right-align">
                    Days
                </div>
                @foreach($eligibilityRenewal as $user)
                    <div class="col s9">
                        <a href="{{ url('user', $user['id']) }}">{{$user['userFullName']}}</a>
                    </div>
                    <div class="col s3 right-align">
                        {{ $user['days'] }}
                    </div>
                @endforeach
            </div>
        </li>
    @endif
    @if ($expiringVisits->count())
        <li>
            <div class="collapsible-header active">Visits Expiring</div>
            <div class="collapsible-body row">
                <div class="col s12">
                    @foreach($expiringVisits as $user)
                        <a href="{{ url('user', $user->id) }}">{{ $user->userFullName }}</a>
                        @foreach($user->visits as $visit)
                            <div class="col s7 small">{{ $visit->smo_code }}</div>
                            <div class="col s5 small">{{ $visit->expiration_date }}</div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </li>
    @endif
    @if ($dueTraining->count())
        <li>
            <div class="collapsible-header active">Overdue Training</div>
            <div class="collapsible-body row">
                <div class="col s12">
                    @foreach($dueTraining as $training)
                        <a href="{{ url('training', $training->id) }}">{{ $training->name }}</a>
                        @foreach($training->assignedUsers as $trainingUser)
                            <div class="col s12 small">
                                <a class="right tiny tooltipped" href="{{url('training/reminder', $trainingUser->id)}}" data-position="right" data-tooltip="Send Reminder">
                                    <i class="material-icons">email</i>
                                </a>
                                {{ $trainingUser->user->userFullName }}
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </li>
    @endif
    </ul>
</div>
