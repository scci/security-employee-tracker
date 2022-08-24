@extends('layouts.master')

@section('title', 'Add Item To Inventory')

@section('content')
    <div class="card" id="new-inventory-item-form">
        <div class="card-content">
            <div class="card-title">Create Inventory Item</div>
            <p>* indicates a required field</p>
            {!! Form::open(array('action' => 'InventoryController@store', 'method' => 'POST', 'class' => 'form container-fluid')) !!}
            @include('inventory._new_item', ['submit' => 'Create'])
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('help')
    @include('inventory._new_inventory_item_help')
@stop
