<nav>
    <div class="nav-wrapper">
        <a class="brand-logo" href="{{url("/")}}"> <img src="{{url("/img/logo-white.png")}}" alt="Security Employee Tracker" /> <span data-toggle="tooltip" data-placement="bottom" title="Security Employee Tracker">{{ $app_name }}</span></a>
        <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>

        <?php

        for($i = 1; $i <= 2; $i++) {
            if($i == 1) {
                $id="nav-mobile"; $class="right hide-on-med-and-down";
            } else {
                $class="side-nav"; $id="mobile-demo";
            }
        ?>
        <ul id="{{$id}}" class="{{$class}}">
            @if($logged_in_user->status == 'active')
                <li><a href="{{url("news")}}">News</a></li>
                @can('view')
                    <li><a href="{{url("user")}}">Users</a></li>
                    @if ($trainingTypes->count()>0)
                        <li><a href="#!" class="dropdown-button" data-activates="training-lists{{$i}}">Trainings<i class="material-icons custom right">arrow_drop_down</i></a></li>
                    @else
                        <li><a href="{{url("training")}}">Trainings</a></li>
                    @endif
                    <li><a href="{{url("group")}}">Groups</a></li>
                @endcan
                <li><a href="#!" class="dropdown-button" data-activates="duty-lists{{$i}}">Security Checks <i class="material-icons custom right">arrow_drop_down</i></a></li>
            @endif
            <li><a href="#!" class="dropdown-button" data-activates="username-dropdown{{$i}}">{{ $logged_in_user->userFullName }} <i class="material-icons custom right">arrow_drop_down</i></a></li>
            <li><a class="modal-trigger waves-effect waves-light" href="#help"><i class="material-icons tooltipped" data-tooltip="Help" data-position="bottom">live_help</i></a></li>
        </ul>

        <?php } ?>
    </div>
</nav>

<?php for($i = 1; $i <= 2; $i++) { ?>

    <ul id="training-lists{{$i}}" class="dropdown-content">
        <li><a href="{{url("training")}}">All</a></li>
        @foreach($trainingTypes as $trainingType)
            <li><a href="{{url('training/trainingtype',$trainingType->id)}}">{{ $trainingType->name }}</a></li>
        @endforeach
    </ul>

<ul id="duty-lists{{$i}}" class="dropdown-content">
    <li><a href="{{ url('duty') }}">All</a></li>
    @foreach($duties as $duty)
        <li><a href="{{url("duty", $duty->id)}}">{{ $duty->name }}</a></li>
    @endforeach
</ul>

<ul id="username-dropdown{{$i}}" class="dropdown-content">
    @can('view')
        <li><a href="{{url("training/completed")}}">Completed Export</a></li>
    @endcan
    @can('edit')
        <li><a class="waves-effect waves-light modal-trigger" href="#jpas-form">JPAS Import</a></li>
        <li><a href="{{url("settings")}}">Administration</a></li>
    @endcan
    <li><a href="{{url("user", Auth::user()->id)}}">My Profile</a></li>
    <li><a href="{{url("logout")}}">Logout</a></li>
</ul>

<?php } ?>
