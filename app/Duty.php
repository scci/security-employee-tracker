<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;

class Duty extends Model
{
    protected $table = 'duties';

    protected $casts = ['has_groups' => 'boolean'];

    public $timestamps = true;

    protected $fillable = ['name', 'cycle', 'description', 'has_groups'];

    public function users()
    {
        return $this->belongsToMany('SET\User')->withPivot('last_worked');
    }

    public function groups()
    {
        return $this->belongsToMany('SET\Group')->withPivot('last_worked');
    }

    public function dutySwaps()
    {
        return $this->belongsToMany('SET\Duty');
    }
}
