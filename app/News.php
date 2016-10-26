<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class News extends Model
{
    protected $table = 'news';
    public $timestamps = true;

    protected $fillable = array('title', 'description', 'publish_date', 'expire_date', 'author_id', 'send_email');

    public function attachments()
    {
        return $this->morphMany('SET\Attachment', 'imageable');
    }
    
    public function author()
    {
        return $this->belongsTo('SET\User', 'author_id');
    }
    
    /**
     * @param $query
     * @param $input
     */
    public function scopePublishedNews($query)
    {
        return $query->where('publish_date', '<=', Carbon::today())
                ->where(function($q) {
                    $q->where('expire_date', '>=', Carbon::today())
                    ->orWhere('expire_date', null);
                });

    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if (is_scalar($value) && $value === '') {
            $value = null;
        }
        return parent::setAttribute($key, $value);
    }
}
