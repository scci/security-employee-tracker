<div class="row">
    <div class="col s12 m6 l4">
        <div class="input-field" id="doc_number_field">
            {!! Form::label('material_control_number', '*Material Control Number:') !!}
            {!! Form::text('material_control_number', null, ['class' => 'validate', 'required']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="type_id_field">
            {!! Form::select('type_id', array(null=>'None') + $types, null, ['class' => 'validate', 'required']) !!}
            {!! Form::label('type_id', '*Type:') !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="classification_id_field">
            @if($submit == 'Update')
                {!! Form::select('classification_id', $classificationTypes, null, ['class' => 'validate', 'required']) !!}
            @else
                {!! Form::select('classification_id', $classificationTypes, array_search('Unclassified', $classificationTypes),
                 ['class' => 'validate', 'required']) !!}
            @endif
            {!! Form::label('classfication_id', '*Classification:') !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="input-field" id="type_field">
        {!! Form::label('description', '*Description:') !!}
        {!! Form::textarea('description', null, ['class' => 'wysiwyg', 'required']) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 m6 l4">
        <div class="input-field" id="media_manufacturer_field">
            {!! Form::label('manufacturer', 'Manufacturer:') !!}
            {!! Form::text('manufacturer', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="media_model_field">
            {!! Form::label('model_number', 'Model:') !!}
            {!! Form::text('model_number', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="serial_number_field">
            {!! Form::label('serial_number', 'Serial Number:') !!}
            {!! Form::text('serial_number', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="asset_tag_number_field">
            {!! Form::label('asset_tag_number', 'Asset Tag:') !!}
            {!! Form::text('asset_tag_number', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4" id="date_created_field">
        {!! Form::label('date_created', '*Date Created:') !!}
        {!! Form::date('date_created', null, ['class' => 'datepicker', 'required']) !!}
    </div>
    <div class="col s12 m6 l4" id="date_into_inventory_field">
        {!! Form::label('date_into_inventory', '*Date Into Inventory:') !!}
        {!! Form::date('date_into_inventory', null, ['class' => 'datepicker', 'required']) !!}
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="received_from_field">
            {!! Form::label('received_from', '*Received From:') !!}
            @if($submit == 'Update')
                {!! Form::text('received_from', null, ['class' => 'validate', 'required']) !!}
            @else
                {!! Form::text('received_from', 'Locally Created', ['class' => 'validate', 'required']) !!}
            @endif
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="received_method_id_field">
            {!! Form::select('received_method_id', array(null=>'None') + $received_methods, null, ['class' => 'validate', 'required']) !!}
            {!! Form::label('received_method_id', '*Received Method:') !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="tracking_number_field">
            {!! Form::label('tracking_number', 'Tracking Number:') !!}
            {!! Form::text('tracking_number', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="room_id_field">
            {!! Form::select('room_id', array(null=>'None') + $rooms, null, ['class' => 'validate', 'required']) !!}
            {!! Form::label('room_id', '*Room:') !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="safe_id_field">
            {!! Form::select('safe_id', array(null=>'None') + $safes, null, ['class' => 'validate']) !!}
            {!! Form::label('safe_id', 'Safe #') !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="drawer_id_field">
            {!! Form::select('drawer_id', array(null=>'None') + $drawers, null, ['class' => 'validate']) !!}
            {!! Form::label('drawer_id', 'Drawer #:') !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="bag_number_field">
            {!! Form::label('bag_number', 'Bag Number:') !!}
            {!! Form::text('bag_number', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="machine_designation_field">
            {!! Form::label('machine_designation', 'Machine Designation:') !!}
            {!! Form::text('machine_designation', null, ['class' => 'validate']) !!}
        </div>
    </div>
    @if($submit == 'Update')
        <div class="col s12 m6 l4">
            <div class="input-field" id="disposition_field">
                {!! Form::label('disposition', 'Disposition:') !!}
                {!! Form::text('disposition', null, ['class' => 'validate']) !!}
            </div>
        </div>
        <div class="col s12 m6 l4" id="disposition_date_field">
            {!! Form::label('disposition_date', 'Disposition Date:') !!}
            {!! Form::date('disposition_date', null, ['class' => 'datepicker']) !!}
        </div>
    @endif
    <div class="col s12 m6 l4">
        <div class="input-field" id="last_inventory_date_and_initials_field">
            {!! Form::label('last_inventory_date_and_initials', 'Date Last Inventoried / By:') !!}
            {!! Form::text('last_inventory_date_and_initials', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="copy_number_field">
            {!! Form::label('copy_number', 'Copy Number:') !!}
            {!! Form::number('copy_number', null, ['class' => 'validate']) !!}
        </div>
    </div>
    <div class="col s12 m6 l4">
        <div class="input-field" id="number_of_copies_field">
            {!! Form::label('number_of_copies', 'Number of Copies:') !!}
            {!! Form::number('number_of_copies', null, ['class' => 'validate']) !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="input-field" id="notes_field">
        {!! Form::label('notes', 'Notes:') !!}
        {!! Form::textarea('notes', null, ['class' => 'wysiwyg']) !!}
    </div>
</div>
<div class="row">
    <div class="col s12 right-align">
        <a class="btn-flat waves-effect waves-indigo" href="{{url("inventory")}}">Back to Inventory</a>
        @if(!isset($inventoryItem) || (isset($inventoryItem) && !$inventoryItem->trashed()))
            {!! Form::submit($submit, array('class' => 'btn-flat waves-effect waves-indigo')) !!}
        @endif
    </div>
</div>