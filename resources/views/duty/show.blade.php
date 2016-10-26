@extends('layouts.master')

@section('title', $duty->name)


@section('content')

    @can('edit')
        <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">

        </div>
    @endcan

    <div class="card">
        <div class="card-content">
            <div class="card-title">
                {{ $duty->name }}
                @can('edit')
                    <a class="btn-flat btn-sm tooltipped" href="{{ action('DutyController@edit', $duty->id) }}" data-tooltip="Edit Security Check">
                        <i class="large material-icons">edit</i>
                    </a>
                @endcan
            </div>
            {{ $duty->description }}
            <table class="bordered">
                <thead>
                <tr>
                    <th>{{ ucfirst(rtrim($duty->cycle, 'ly')) }}</th>
                    <th>Employee</th>
                    @can('edit')
                        <th class="center-align hide-on-print">Swap
                            <a tabindex="0" role="button" data-trigger="focus" data-toggle="popover" data-placement="top" title="Performing a swap." data-content="You must click the swap button (up/down icon) on two different rows to make a swap. When selected, the button will turn green." aria-hidden="true"><span class="glyphicon glyphicon-question-sign"></span></a>
                        </th>
                    @endcan
                </tr>
                </thead>
                <tbody>
                    @foreach ($list as $row)
                        <tr>
                            <td>{{ $row['date'] }}</td>
                            <td>
                                {!! $row['row'] !!}
                            </td>
                            @can('edit')
                                <td class="center-align hide-on-print">
                                    <button type="button" class="duty-swap btn-flat btn-sm" aria-label="swap" data-id="{{ $row['id'] }}" data-duty="{{ $duty->id }}" data-date="{{ $row['date'] }}" data-type="{{ $duty->has_groups ? 'Group' : 'User' }}">
                                        <i class="material-icons">swap_vert</i>
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@stop

@section('help')

    @can('edit')
        <strong>Edit this security check</strong>
        <p>Click on the pencil icon next to the name.</p>

        <strong>Swapping users</strong>
        <p>You may swap users by clicking on the swap icon on two different rows. <br />
        Swapping is a one time instance. And the user will resume their place for all future instances.</p>

        <strong>Sorting</strong>
        <p>All entries are sorted alphabetically (either by their last name or group name).</p>
    @endcan
@stop