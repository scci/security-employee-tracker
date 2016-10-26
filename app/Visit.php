<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    /**
     * @var string
     */
    protected $table = 'visits';
    /**
     * @var bool
     */
    public $timestamps = true;
    /**
     * @var array
     */
    protected $fillable = array('smo_code', 'poc', 'phone', 'comment', 'visit_date', 'expiration_date', 'author_id', 'user_id');

    public function user()
    {
        return $this->belongsTo('SET\User');
    }

    public function author()
    {
        return $this->belongsTo('SET\User', 'author_id');
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
}
