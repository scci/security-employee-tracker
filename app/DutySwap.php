<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;

class DutySwap extends Model
{
    protected $table = 'duty_swaps';

    public $timestamps = true;

    protected $fillable = ['imageable_id', 'imageable_type', 'duty_id', 'date'];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function duty()
    {
        return $this->belongsTo('SET\Duty');
    }
}
