<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Setting.
 */
class Setting extends Model
{
    protected $table = 'settings';
    public $timestamps = true;
    public $incrementing = false;
    protected $primaryKey = 'name';

    protected $fillable = ['name', 'primary', 'secondary'];
}
