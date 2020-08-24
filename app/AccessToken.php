<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;



class AccessToken extends Model
{
    //
    protected $table = 'access_tokens';
   
    protected $fillable = ['name'];
    
    

    public function user()
    {
        return $this->belongsTo('SET\User');
    }

}
