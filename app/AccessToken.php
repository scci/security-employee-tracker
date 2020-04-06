<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;



class AccessToken extends Model
{
    //
    protected $table = 'access_tokens';
   
    protected $fillable = ['user_id','sipr_issued','sipr_issue_date', 'sipr_expiration_date', 'sipr_return_date', 'cac_issued', 'cac_issue_date','cac_expiration_date', 'cac_return_date'];
    
    

    public function user()
    {
        return $this->belongsTo('SET\User');
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
