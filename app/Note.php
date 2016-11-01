<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $table = 'notes';
    public $timestamps = true;

    protected $fillable = ['user_id', 'author_id', 'private', 'alert',
        'comment', 'title', ];

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

    public function training()
    {
        return $this->belongsTo('SET\Training');
    }
}
