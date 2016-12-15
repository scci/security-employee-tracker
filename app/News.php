<?php

namespace SET;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use SET\Handlers\DateFormat;
use SET\Mail\SendNewsEmail;

class News extends Model
{
    use DateFormat;

    protected $table = 'news';
    public $timestamps = true;

    protected $fillable = ['title', 'description', 'publish_date', 'expire_date', 'author_id', 'send_email'];

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
     */
    public function scopePublishedNews($query)
    {
        return $query->where('publish_date', '<=', Carbon::today())
                ->where(function ($q) {
                    $q->where('expire_date', '>=', Carbon::today())
                    ->orWhere('expire_date', null);
                });
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

    public function getPublishDateAttribute()
    {
        return $this->dateFormat($this->attributes['publish_date']);
    }

    public function getExpirationDateAttribute()
    {
        return $this->dateFormat($this->attributes['expire_date']);
    }

    /**
     * Email the news on the publish_date when a news article is created or updated.
     */
    public function emailNews()
    {
        $publishDate = Carbon::createFromFormat('Y-m-d', $this->publish_date);

        if ($this->send_email && $publishDate->eq(Carbon::now())) {
            $users = User::skipSystem()->active()->get();
            Mail::bcc($users)->send(new SendNewsEmail($this));
        }
    }
}
