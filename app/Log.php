<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    /**
     * @var string
     */
    protected $table = 'logs';
    /**
     * @var bool
     */
    public $timestamps = true;
    /**
     * @var array
     */
    protected $fillable = ['comment', 'author_id', 'user_id', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo('SET\User');
    }

    public function author()
    {
        return $this->belongsTo('SET\User', 'author_id');
    }
}
