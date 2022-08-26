@extends('layouts.master')

@section('title', 'Inventory List')

@section('content')


    <div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
        <a class="btn-floating btn-large red tooltipped modal-trigger" href="{{ url('/inventory/create') }}" data-position="left" data-tooltip="Inventory Item">
            <i class="large material-icons">add</i>
        </a>
    </div>
    <div class="card">
        <div class="card-content">
            <span class="card-title">Inventory List</span>
            @include('inventory._item_changes_success_banner')
                <table class="row-border hover data-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Material Control Number</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Serial Number</th>
                            <th>Classification</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventoryList as $item)
                            <tr class="user-{{$item->id}}">
                                <td>
                                    @if($item->trashed())
                                        <span class="tooltipped" data-position="right" data-tooltip="Destroyed">
                                            <i class="material-icons red-text">remove_circle</i>
                                        </span>
                                    @endif
                                </td>
                                <td><a href="{{ url('/inventory/'. $item->id. '/edit') }}">{{ $item->material_control_number }}</a></td>
                                <td>
                                    {{ isset($item->type) ? $item->type->name : '' }}
                                </td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->serial_number }}</td>
                                <td>{{ isset($item->classification) ? $item->classification->name : ''}}</td>
                                <td class="no-wrap">
                                    <div class="action-buttons">
                                    @can('edit')
                                        <a href="{{ url("/inventory/$item->id/edit") }}" class="btn-flat btn-sm tooltipped" data-position="top" data-tooltip="Edit">
                                            <i class="material-icons">mode_edit</i>
                                        </a>
                                    @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.data-table').DataTable( {
                destroy: true,
                "order": [],
                paging: false
            } );
        } );
    </script>
@stop

@section('help')
    @include('inventory._new_inventory_item_help')
@stop
