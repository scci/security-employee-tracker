<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';
    public $timestamps = true;

    protected $fillable = ['name', 'closed_area'];

    protected $casts = ['closed_area' => 'boolean'];

    public function users()
    {
        return $this->belongsToMany('SET\User')->withPivot('access');
    }

    public function trainings()
    {
        return $this->belongsToMany('SET\Training');
    }

    public function duties()
    {
        return $this->belongsToMany('SET\Duty');
    }

    public function dutySwap()
    {
        return $this->morphMany('SET\DutySwap', 'imageable');
    }

    /**
     * Allows you to get only active users.
     *
     * @param $query
     * @return mixed
     */
    public function scopeActiveUsers($query)
    {
        return $query->whereHas('users', function ($q) {
            $q->where('status', 'active');
        });
    }
}
