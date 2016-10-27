<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Setting
 * @package SET
 *
 * Handles the varous settings through the project and is updated/set in the admin panel.
 */
class Setting extends Model
{
    protected $table = 'settings';
    public $timestamps = true;
    public $incrementing = false;
    protected $primaryKey = 'name';

    protected $fillable = array('name', 'primary', 'secondary');
}
