<style type="text/css">
    th, td {padding: 10px}
    td {border-top: solid 1px #ddd;}
    .even {background-color: #f9f9f9}
    .odd {background-color: #fff}
</style>

@if(!$notes->isEmpty())
    <h2>Training Due</h2>
    <p>Below is a list of your employees who have training that will be due soon or is past due.
        They have also received a reminder email as well.</p>
    <table cellspacing="0" cellpadding="5">
        <tr>
            <th style="border-bottom:solid 1px black;">Name</th>
            <th style="border-bottom:solid 1px black;">Training</th>
            <th style="border-bottom:solid 1px black;">Due Date</th>
        </tr>
        <?php $i = 0; ?>
        @foreach($notes as $note)
            <tr class="{{ ($i % 2 == 0 ? 'even' : 'odd') }}">
                <td style="border-bottom:solid 1px black;">{{ $note->user->userFullName }}</td>
                <td style="border-bottom:solid 1px black;">{{ $note->training->name }}</td>
                <td style="border-bottom:solid 1px black; white-space: nowrap">{{ $note->due_date }}</td>
            </tr>
            <?php $i++; ?>
        @endforeach
    </table>
@endif

