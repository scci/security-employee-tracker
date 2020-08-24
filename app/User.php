<?php

namespace SET;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class User.
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * @var string
     */
    protected $table = 'users';
    /**
     * @var bool
     */
    public $timestamps = true;
    /**
     * @var array
     */
    protected $fillable = ['username', 'emp_num', 'first_name', 'nickname', 'last_name',
        'email', 'phone', 'status', 'clearance', 'elig_date', 'inv', 'inv_close', 'destroyed_date',
        'supervisor_id', 'access_level', 'password', 'separated_date', 'cont_eval', 'cont_eval_date', ];
    /**
     * @var array
     */
    protected $hidden = ['username', 'password', 'remember_token'];

    /*
     * @var array $logAttributes defines will log the changed attributes
     */
    use LogsActivity;
    protected static $logAttributes = ['username', 'emp_num', 'first_name', 'nickname',
         'last_name', 'email', 'phone', 'jpas_name', 'status', 'clearance',
         'elig_date', 'inv', 'inv_close', 'destroyed_date', 'role', 'supervisor_id', 'access_level',
         'last_logon', 'ip', 'cont_eval', 'cont_eval_date', ];

    /**
     * make destroyed_date a Carbon instance.
     */
    protected $dates = ['destroyed_date'];

    public function supervisor()
    {
        return $this->belongsTo('SET\User', 'supervisor_id');
    }

    public function subordinates()
    {
        return $this->hasMany('SET\User', 'supervisor_id');
    }

    public function attachments()
    {
        return $this->morphMany('SET\Attachment', 'imageable');
    }

    public function notes()
    {
        return $this->hasMany('SET\Note');
    }

    public function travels()
    {
        return $this->hasMany('SET\Travel');
    }

    public function visits()
    {
        return $this->hasMany('SET\Visit');
    }

    public function assignedTrainings()
    {
        return $this->hasMany('SET\TrainingUser', 'user_id');
    }

    public function trainingUsers()
    {
        return $this->hasMany('SET\TrainingUser', 'user_id');
    }
    
    public function userAccessTokens()
    {
        return $this->hasMany('SET\UserAccessToken', "user_id");
    }

    public function addOrUpdateUserAccessTokens($userId, $newAccessToken, $data)
    {
        if (isset($data['userAccessTokens'])){
            $userAccessTokens = $data['userAccessTokens'];
        }

        $user = $this->findOrFail($userId);
        if(isset($newAccessToken['token_id'])){

            $user->userAccessTokens()->updateOrCreate([
                'user_id' => $userId,
                'token_id' => $newAccessToken['token_id'],
                'token_issue_date' => $newAccessToken['token_issue_date'],
                'token_expiration_date' => $newAccessToken['token_expiration_date'],
                'token_return_date' => $newAccessToken['token_return_date'],
                ]);
        }
        
        if (isset($userAccessTokens)) {
            foreach ($userAccessTokens as $key => $token) {
                $user->userAccessTokens()->updateOrCreate([
                    'user_id' => $userId,
                    'token_id' => $key],
                    [
                    'token_issue_date' => $token['token_issue_date'],
                    'token_expiration_date' => $token['token_expiration_date'],
                    'token_return_date' => $token['token_return_date'],
                ]);
            }
        }
        return;
    }

     /**
     * Sort userAccess Tokens so CAC is first and SIPR TOKEN is second
     * 
     */

    public function sortedUserAccessTokenCollection()
    {
        $accessTokens = AccessToken::all();
        if($accessTokens->isEmpty() ){ // if Access Tokens have not been created yet then return
            return $this->userAccessTokens();
        }
        if($accessTokens->where('name', 'SIPR TOKEN')->isEmpty() && $accessTokens->where('name', 'CAC')->isEmpty()){ // if CAC and SIPR TOKEN access tokens are not created 
            $userAccessTokens = $this->userAccessTokens()->sortBy('name');
           
        } elseif($accessTokens->where('name', 'CAC')->isNotEmpty() && $accessTokens->where('name', 'SIPR TOKEN')->isEmpty()){ // if CAC access token has been created
           
            $userAccessTokens = $this->userAccessTokens()->where('token_id', '!=', $accessTokens->where('name', 'CAC')->pluck('id'))->get()->sortBy('name');
           
        } elseif($accessTokens->where('name', 'SIPR TOKEN')->isNotEmpty() && $accessTokens->where('name', 'CAC')->isEmpty() ){ // if SIPR TOKEN access token has been created
            
            $userAccessTokens = $this->userAccessTokens()->where('token_id', '!=', $accessTokens->where('name', 'SIPR TOKEN')->pluck('id'))->get()->sortBy('name');
            
        } elseif($accessTokens->where('name', 'SIPR TOKEN')->isNotEmpty() && $accessTokens->where('name', 'CAC')->isNotEmpty()){

            // These else if statments could be replaced with the following variable if it were assumed that CAC and SIPR token would exist in the system 100% of the time
            $userAccessTokens = $this->userAccessTokens()->where([['token_id', '!=', $accessTokens->where('name', 'SIPR TOKEN')->pluck('id')],
            ['token_id', '!=', $accessTokens->where('name', 'CAC')->pluck('id')]])->get()->sortBy('name');
        }
        // if sipr token has been created and has been assigned to user
        if($accessTokens->where('name', 'SIPR TOKEN')->isNotEmpty() &&
         $this->userAccessTokens()->where('token_id', $accessTokens->where('name', 'SIPR TOKEN')->pluck('id'))->first() != null){
       
            $siprTokenAccessToken = $this->userAccessTokens()->where('token_id', $accessTokens->where('name', 'SIPR TOKEN')->pluck('id'))->first();
            $userAccessTokens->prepend($siprTokenAccessToken);
        }
        // if cac access token has been created and has been assigned to user
        if($accessTokens->where('name', 'CAC')->isNotEmpty() &&
         $this->userAccessTokens()->where('token_id', $accessTokens->where('name', 'CAC')->pluck('id'))->first() != null){
            
            $cacAccessToken = $this->userAccessTokens()->where('token_id', $accessTokens->where('name', 'CAC')->pluck('id'))->first();
            $userAccessTokens->prepend($cacAccessToken);
        }

        return $userAccessTokens;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function trainings()
    {
        return $this->belongsToMany('SET\Training');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany('SET\Group')->withPivot('access');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function duties()
    {
        return $this->belongsToMany('SET\Duty');
    }

    /**
     * If we have a nickname, return 'lastname, nickname' otherwise return 'lastname, firstname'.
     *
     * @return string
     */
    public function getUserFullNameAttribute()
    {
        if ($this->attributes['id'] == 1) {
            return 'system';
        }

        $firstName = $this->attributes['first_name'];

        if ($this->attributes['nickname']) {
            $firstName = $this->attributes['first_name'].' ('.$this->attributes['nickname'].')';
        }

        if (Setting::get('full_name_format') == 'first_last') {
            return $firstName.' '.$this->attributes['last_name'];
        }

        return $this->attributes['last_name'].', '.$firstName;
    }

    /**
     * @param $query
     * @param $input
     */
    public function scopeSearchUsers($query, $input)
    {
        return $query->where('first_name', 'LIKE', "%$input%")
            ->orWhere('last_name', 'LIKE', "%$input%")
            ->orWhere('emp_num', 'LIKE', "%$input%");
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSkipSystem($query)
    {
        return $query->where('id', '>', 1);
    }


     /**
     * @param string $query
     *
     * @return array 
     */
    public function scopeUserStatus($query)
    {
        $query->when( session('userStatus'), function( $user ){ 
            return $user->where( 'status', session('userStatus') );
         });
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

    /**
     * @param $user
     * Obtain the Activitylog for the designated user or for all users.
     * Populate log array values ["comment", "updated_at", "user_fullname"]
     *
     * @return Log collection
     */
    public function getUserLog($user = null)
    {
        $ignoreList = ['password', 'last_logon', 'remember_token', 'ip'];
        $record = $logs = []; // define arrays

        foreach (($user) ? $user->activity : Activity::all() as $entry) {
            $record['updated_at'] = $entry->updated_at;
            $record['user_fullname'] = $entry->properties['attributes']['last_name']
            .', '.$entry->properties['attributes']['first_name'];

            $record['comment'] = '';
            if ($entry->description == 'updated') { // Report all changes on each update
                $result = $this->arrayRecursiveDiff($entry->changes->get('attributes'),
              $entry->changes->get('old'));
                foreach ($result as $key => $value) {
                    if (!in_array($key, $ignoreList)) {
                        $record['comment'] .=
                ucfirst($key).' '.$entry->description." from '"
                .$entry->changes->get('old')[$key]."' to '".$value."'.\n";
                    }
                }
            } else { // description == 'created' ||  'deleted'
                $record['comment'] .= $entry->description." user '".$record['user_fullname']."'.\n";
            }

            // Append only non-ignored record entries to log
            if ($record['comment']) {
                array_push($logs, $record);
            }
        }

        return collect($logs)->sortByDesc('updated_at');  // return latest -> earliest
    }

    /**
     * @param Array1 Array2
     * As array_diff function only checks one dimension of a n-dimensional array.
     * arrayRecursiveDiff will compare n-dimensional.
     * Will insure compared objects are arrays.
     *
     * @return DiffsArray
     */
    private function arrayRecursiveDiff($aArray1, $aArray2)
    {
        $aReturn = [];
        if (!is_array($aArray1) || !is_array($aArray2)) {
            return $aReturn;
        }
        foreach ($aArray1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = $this->arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) {
                        $aReturn[$mKey] = $aRecursiveDiff;
                    }
                } else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $mValue;
                    }
                }
            } else {
                $aReturn[$mKey] = $mValue;
            }
        }

        return $aReturn;
    }

    public function getDestroyDate($status)
    {
        if ($status == 'active') {
            return;
        }

        if ($status == 'separated') {
            return Carbon::today()->addYears(2)->startOfWeek();
        }

        return Carbon::today()->addWeek()->startOfWeek();
    }
}
