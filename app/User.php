<?php

namespace SET;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User.
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
    protected $fillable = ['username', 'emp_num', 'first_name', 'nickname', 'last_name',
        'email', 'phone', 'status', 'clearance', 'elig_date', 'inv', 'inv_close', 'destroyed_date',
        'supervisor_id', 'access_level', 'password', ];
    /**
     * @var array
     */
    protected $hidden = ['username', 'password', 'remember_token'];

    /**
     * make destroyed_date a Carbon instance.
     */
    protected $dates = ['destroyed_date'];

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

    /**
     * If we have a nickname, return 'lastname, nickname' otherwise return 'lastname, firstname'.
     *
     * @return string
     */
    public function getUserFullNameAttribute()
    {
        if ($this->attributes['id'] == 1) {
            return 'system';
        }

        $firstName = $this->attributes['first_name'];

        if ($this->attributes['nickname']) {
            $firstName = $this->attributes['first_name'].' ('.$this->attributes['nickname'].')';
        }

        if (Setting::get('full_name_format') == 'first_last') {
            return $firstName.' '.$this->attributes['last_name'];
        }

        return $this->attributes['last_name'].', '.$firstName;
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
     *
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
     * Store empty values as null in the DB
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if (is_scalar($value) && $value === '') {
            $value = null;
        }

        return parent::setAttribute($key, $value);
    }

    public function getDestroyDate($status)
    {
        if ($status == 'active') {
            return;
        }

        if ($status == 'separated') {
            return Carbon::today()->addYears(2)->startOfWeek();
        }

        return Carbon::today()->addWeek()->startOfWeek();
    }
}
