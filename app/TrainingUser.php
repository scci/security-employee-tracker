<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;

class TrainingUser extends Model
{
    /**
     * @var string
     */
    protected $table = 'training_user';
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $fillable = array('training_id', 'due_date', 'completed_date', 'comment', 'author_id', 'user_id');

    public function user()
    {
        return $this->belongsTo('SET\User');
    }

    public function author()
    {
        return $this->belongsTo('SET\User', 'author_id');
    }

    public function training()
    {
        return $this->belongsTo('SET\Training');
    }

    public function attachments()
    {
        return $this->morphMany('SET\Attachment', 'imageable');
    }

    /**
     * Allows you to call activeUsers() on notes
     * to return only notes with active users.
     *
     * @param $query
     * @return mixed
     */
    public function scopeActiveUsers($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('status', 'active');
        });
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
