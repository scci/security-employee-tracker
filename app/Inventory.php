<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;
    protected $table = 'inventory';
    protected $dates = ['deleted_at'];
    protected $fillable = ['material_control_number', 'type_id', 'description','classification_id', 'manufacturer',
        'model_number', 'serial_number', 'asset_tag_number', 'date_created', 'date_into_inventory', 'received_from',
        'received_method_id', 'tracking_number', 'room_id', 'safe_id', 'drawer_id', 'bag_number', 'machine_designation',
        'disposition', 'disposition_date', 'last_inventory_date_and_initials', 'notes', 'copy_number', 'number_of_copies'];

   public function type()
    {
       return $this->hasOne('SET\InventoryType', 'id', 'type_id' );
    }

    public function classification()
    {
        return $this->hasOne('SET\InventoryClassification', 'id', 'classification_id');
    }

    public function received_method()
    {
        return $this->hasOne('SET\InventoryReceiveMethod', 'id', 'received_method_id' );
    }

    public function room()
    {
        return $this->hasOne('SET\InventoryRoom', 'id', 'room_id');
    }

    public function safe()
    {

        return $this->hasOne('SET\InventorySafe', 'id', 'safe_id');
    }

    public function drawer()
    {
        return $this->hasOne('SET\InventoryDrawer', 'id', 'drawer_id');
    }
}
