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
}
