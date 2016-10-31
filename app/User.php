<?php

namespace SET;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package SET
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * @var string
     */
    protected $table = 'users';
    /**
     * @var bool
     */
    public $timestamps = true;
    /**
     * @var array
     */
    protected $fillable = array('username', 'emp_num', 'first_name', 'nickname', 'last_name',
        'email', 'phone', 'status', 'clearance', 'elig_date', 'inv', 'inv_close', 'destroyed_date',
        'supervisor_id', 'access_level', 'password');
    /**
     * @var array
     */
    protected $hidden = array('username', 'password', 'remember_token');

    public function supervisor()
    {
        return $this->belongsTo('SET\User', 'supervisor_id');
    }

    public function subordinates()
    {
        return $this->hasMany('SET\User', 'supervisor_id');
    }

    public function attachments()
    {
        return $this->morphMany('SET\Attachment', 'imageable');
    }

    public function notes()
    {
        return $this->hasMany('SET\Note');
    }

    public function travels()
    {
        return $this->hasMany('SET\Travel');
    }

    public function visits()
    {
        return $this->hasMany('SET\Visit');
    }

    public function assignedTrainings()
    {
        return $this->hasMany('SET\TrainingUser', 'user_id');
    }

    public function trainingUsers()
    {
        return $this->hasMany('SET\TrainingUser', 'user_id');
    }

    public function logs()
    {
        return $this->hasMany('SET\Log');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function trainings()
    {
        return $this->belongsToMany('SET\Training');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany('SET\Group')->withPivot('access');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function duties()
    {
        return $this->belongsToMany('SET\Duty');
    }

    public function dutySwap()
    {
        return $this->morphMany('SET\DutySwap', 'imageable');
    }

    public function news()
    {
        return $this->hasMany('SET\News');
    }

    /**
     * If we have a nickname, return 'lastname, nickname' otherwise return 'lastname, firstname'
     * @return string
     */
    public function getUserFullNameAttribute()
    {
        if ($this->attributes['id'] == 1) {
            $fullName = 'system';
        } elseif ($this->attributes['nickname']) {
            $fullName = $this->attributes['last_name']
                . ', ' . $this->attributes['first_name']
                . ' (' . $this->attributes['nickname'] . ')';
        } else {
            $fullName = $this->attributes['last_name'] . ', ' . $this->attributes['first_name'];
        }
        return $fullName;
    }

    /**
     * @param $query
     * @param $input
     */
    public function scopeSearchUsers($query, $input)
    {
        return $query->where('first_name', 'LIKE', "%$input%")
            ->orWhere('last_name', 'LIKE', "%$input%")
            ->orWhere('emp_num', 'LIKE', "%$input%");
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSkipSystem($query)
    {
        return $query->where('id', '>', 1);
    }


    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     *
     */
    public function setAttribute($key, $value)
    {
        if (is_scalar($value) && $value === '') {
            $value = null;
        }

        return parent::setAttribute($key, $value);
    }

}
