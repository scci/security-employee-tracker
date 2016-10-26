
<style type="text/css"> th, td {padding: 10px} td {border-top: solid 1px #ddd;} .even {background-color: #f9f9f9} .odd {background-color: #fff}</style>

@if($notes->count())
    <h2>Training Due</h2>
    <p>The below is a list of upcoming and past due training. All users listed below have received an email reminder.</p>
    <table cellspacing="0" cellpadding="5">
        <tr>
            <th style="border-bottom:solid 1px black;">Name</th>
            <th style="border-bottom:solid 1px black;">Training</th>
            <th style="border-bottom:solid 1px black;">Due Date</th>
        </tr>
        @foreach($notes as $note)
            <tr style="background-color: {{ $note->due_date <= Carbon\Carbon::today() ? '#F2DEDE' : '' }} ">
                <td style="border-bottom:solid 1px black;">{{ $note->user->userFullName }}</td>
                <td style="border-bottom:solid 1px black;">{{ $note->training->name }}</td>
                <td style="border-bottom:solid 1px black; white-space: nowrap">{{ $note->due_date }}</td>
            </tr>
        @endforeach
    </table>
@endif
@if($visits->count())
    <h2>Visits Expiring</h2>
    <p>The below is a list of visitation rights that are expiring this week:</p>
    <table cellspacing="0" cellpadding="5">
        <tr>
            <th>Name</th>
            <th>Location</th>
            <th>Expiration Date</th>
        </tr>
        <?php $i = 0; ?>
        @foreach($visits as $visit)
            <tr style="background-color: {{ $visit->due_date <= Carbon\Carbon::today() ? '#F2DEDE' : '' }} " class="{{ ($i % 2 == 0 ? 'even' : 'odd') }}">
                <td>{{ $visit->user->userFullName }}</td>
                <td>{{ $visit->note }}</td>
                <td style="white-space: nowrap">{{ $visit->due_date }}</td>
            </tr>
            <?php $i++; ?>
        @endforeach
    </table>
@endif

@if($records->count())
<h2>Training has been auto-renewed as follows:</h2>

<table cellspacing="0" cellpadding="5">
    <tr>
        <th>Name</th>
        <th>Training</th>
        <th>Due Date</th>
    </tr>
    <?php $i = 0; ?>
    @foreach($records as $record)
        <tr class="{{ ($i % 2 == 0 ? 'even' : 'odd') }}">
            <td>{{ $record['name'] }}</td>
            <td>{{ $record['training'] }}</td>
            <td style="white-space: nowrap">{{ $record['due_date'] }}</td>
        </tr>
        <?php $i++; ?>
    @endforeach
</table>
@endif

@if($destroyed->count())
    <h2>Users Destroyed</h2>
    <p>The following users have been deleted from the system:</p>
    <ul>
        @foreach($destroyed as $user)
            <li>{{ $user->userFullName }}</li>
        @endforeach
    </ul>
    Note: If these users still exist within LDAP, they will be recreated on the next user login to SET.
@endif

@if($dutyLists->count())
<h2>Security Checks for this week</h2>


    @foreach($dutyLists as $dutyList)

        <p>
            <strong>{{ $dutyList['duty']->name }}</strong> <br />

                @foreach($dutyList as $row)
                    @if (!is_null($row['date']) && \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::createFromFormat('Y-m-d', $row['date'])) < 7)
                        {{$row['date']}} -

                        @foreach($row['users'] as $user)
                            {{ $user->userFullName }} ;
                        @endforeach
                        <br />
                    @endif
                @endforeach

            <br />
        </p>


    @endforeach

@endif