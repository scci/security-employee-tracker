<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';
    public $timestamps = true;

    protected $fillable = array('name', 'closed_area');

    protected $casts = ['closed_area' => 'boolean'];

    public function users()
    {
        return $this->belongsToMany('SET\User')->withPivot('access');
    }

    public function trainings()
    {
        return $this->belongsToMany('SET\Training');
    }
    
    public function duties() {
        return $this->belongsToMany('SET\Duty');
    }

    public function dutySwap()
    {
        return $this->morphMany('SET\DutySwap', 'imageable');
    }
}
