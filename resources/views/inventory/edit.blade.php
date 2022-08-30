@extends('layouts.master')

@section('title', 'Edit Inventory Item')

@section('content')
    <div class="card" id="edit-inventory-form">
        <div class="card-content">
            <div class="card-title">Edit Inventory Item</div>
            @if($inventoryItem->disposition && $inventoryItem->disposition_date && !$inventoryItem->trashed())
                {!! Form::open(array('action' => ['InventoryController@destroy', $inventoryItem->id], 'method' => 'DELETE', 'class' => 'form-inline')) !!}
                    <div class="row">
                        <div class="col s12">
                            <p style="display: inline-flex;
                                        align-items: center;
                                        margin-bottom:1em;
                                        padding: .5em 1em;
                                        border: solid #DE911D;
                                        background: #FFF3C4;
                                        border-radius:5px;
                                        border-width: thin">
                                <i class="material-icons " style="color:#DE911D;">warning</i>
                                <span style="padding-left: 1em;">
                                    <strong>Warning: </strong>
                                    Once destroyed, the inventory item can no longer be updated
                                </span>
                            </p>
                        </div>
                        <div class="col s12">
                            <button style="background: #F44336;" class="btn" type="submit" id="js-destroyInventoryItem">Destroy</button>
                        </div>
                    </div>
                {!! Form::close() !!}
            @elseif($inventoryItem->trashed())
                <div class="col s12">
                <p style="display: inline-flex;
                                align-items: center;
                                margin-bottom:1em;
                                padding: .5em 1em;
                                border: solid #F44336;
                                background: #FACDCD;
                                border-radius:5px;
                                border-width: thin">
                    <i class="material-icons red-text">remove_circle</i>
                    <span style="padding-left: 1em;"><strong style="font:larger;">Destroyed</strong></span>
                </p>
                </div>
            @endif
            {!! Form::model($inventoryItem, array('action' => ['InventoryController@update', $inventoryItem->id], 'method' => 'PATCH', 'class' => 'form container-fluid')) !!}
                @include('inventory._new_item', ['submit' => 'Update'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')
    @include('inventory._new_inventory_item_help')
@stop
