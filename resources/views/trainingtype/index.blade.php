@extends('layouts.master')

@section('title', 'Training Type Directory')

@section('content')

    @can('edit')
        <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
            <a class="btn-floating btn-large red tooltipped modal-trigger" href="{{ url('/trainingtype/create') }}"
                data-position="left" data-tooltip="Create Training Type">
                <i class="large material-icons">add</i>
            </a>
        </div>
    @endcan

    <div class="card">
        <div class="card-content">
            <span class="card-title">Training Types</span>
            <table class="row-border hover data-table striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Description</th>
                        <!-- <th>sidebar</th> -->
                        @can('edit')<th>Modify</th>@endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trainingtypes as $trainingtype)
                        <tr class="trainingtype-{{$trainingtype->id}}">
                            <td><a href="{{ url('/trainingtype', $trainingtype->id) }}">{{ $trainingtype->name }}</a></td>
                            <td>{{ $trainingtype->status>0?'Active':'Inactive' }}</td>
                            <td>{{ $trainingtype->description }}</td>
                            <!-- <td>{{ $trainingtype->sidebar }}</td> -->

                            @can('edit')
                            <td class="no-wrap">
                                <div class="action-buttons">
                                    <a href="{{ url("/trainingtype/$trainingtype->id/edit") }}"
                                        class="btn-flat btn-sm tooltipped" data-position="top" data-tooltip="Edit">
                                        <i class="material-icons">mode_edit</i>
                                    </a>
                                    <button type="button" class="btn-flat btn-sm delete-training-type tooltipped"
                                        data-id="{{ $trainingtype->id }}" data-position="top" data-tooltip="Delete">
                                        <i class="material-icons">delete</i>
                                    </button>
                                </div>
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
        <strong>Creating a new item</strong>
        <p>To create a new item, click on the New type button (big red button) on the bottom right of your screen.</p>

        <strong>Making changes</strong>
        <ul class="browser-default">
            <li>You may update the item via edit icon.</li>
            <li>You may delete the item via delete icon.  Deleting the type will disassociate the type from the training.</li>
        </ul>
    @endcan

    <strong>Sorting</strong>
    <p>You may sort any column by clicking on the column header.</p>

    <strong>Searching</strong>
    <p>When searching, the table will automatically hide any rows that are not part of the search criteria. All columns are searched.</p>
@stop
