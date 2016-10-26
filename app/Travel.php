<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;

class Travel extends Model
{
    /**
     * @var string
     */
    protected $table = 'travels';
    /**
     * @var bool
     */
    public $timestamps = true;
    /**
     * @var array
     */
    protected $fillable = array('location', 'brief_date', 'debrief_date', 'leave_date', 'return_date', 'comment', 'is_ready', 'author_id', 'user_id');
    
    public function user()
    {
        return $this->belongsTo('SET\User');
    }
    
    public function author()
    {
        return $this->belongsTo('SET\User', 'author_id');
    }

    public function attachments()
    {
        return $this->morphMany('SET\Attachment', 'imageable');
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
