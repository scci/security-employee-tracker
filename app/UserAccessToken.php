<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;



class UserAccessToken extends Model
{
    //
    protected $table = 'user_access_tokens';
   
    protected $fillable = ['user_id', 'token_id', 'token_issued','token_issue_date', 'token_expiration_date', 'token_return_date'];
    
    

    public function user()
    {
        return $this->belongsTo('SET\User', 'user_id');
    }

    public function accessToken()
    {
        return $this->belongsTo('SET\AccessToken', 'token_id');
    }

    /**
     * Store empty values as null in the DB.
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



}
