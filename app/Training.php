<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $table = 'trainings';
    public $timestamps = true;

    protected $fillable = ['name', 'renews_in', 'description', 'administrative', 'training_type_id'];
    protected $dates = ['created_at', 'updated_at'];
    protected $appends = ['incompleted'];

    public function assignedUsers()
    {
        return $this->hasMany('SET\TrainingUser', 'training_id');
    }

    public function users()
    {
        return $this->belongsToMany('SET\User');
    }

    public function groups()
    {
        return $this->belongsToMany('SET\Group');
    }

    public function attachments()
    {
        return $this->morphMany('SET\Attachment', 'imageable');
    }

    public function getIncompletedAttribute()
    {
        return $this->users()
            ->whereNull('training_user.completed_date')
            ->active()
            ->count();
    }

    /**
     * @param $query
     * @param $input
     */
    public function scopeSearchTraining($query, $input)
    {
        return $query->where('name', 'LIKE', "%$input%");
    }

    /**
    * Get the training type for the training.
    */
    public function trainingType()
    {
        return $this->belongsTo('SET\TrainingType', 'training_type_id'); // One To Many (Inverse)
    }
}
