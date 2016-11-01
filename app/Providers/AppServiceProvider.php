<?php

namespace SET\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use SET\Log;
use SET\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // If the save() function is used on the User model, we will generate a new log note.
        User::updating(function ($user) {
            $this->createUserLog($user);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() == 'local') {
            $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');
        }
    }

    /**
     * Check's for what is being changed when save() is called on a User model
     * and creates a new note for the change being made.
     *
     * @param $user
     *
     * @return bool
     */
    private function createUserLog($user)
    {
        $ignoreList = ['password', 'last_logon', 'remember_token'];

        $log = [];
        $log['comment'] = '';
        if ($user->isDirty()) {
            $log = $this->buildLogComment($user, $ignoreList, $log);

            $log['author_id'] = Auth::user() ? Auth::user()->id : 1;
            $log['user_id'] = $user->id;
            if (!empty($log['comment'])) {
                Log::create($log);
            }
        }

        return true;
    }

    /**
     * @param $user
     * @param string[] $ignoreList
     * @param $log
     */
    private function buildLogComment($user, $ignoreList, $log)
    {
        foreach ($user->getDirty() as $key => $value) {
            $original = $user->getOriginal($key);
            if (!in_array($key, $ignoreList) && !$this->nullToEmptyString($original, $value)) {
                $log['comment'] .= ucfirst($key)." changed from '".$original."' to '".$value."'.\n";
            }
        }

        return $log;
    }

    private function nullToEmptyString($oldValue, $newValue)
    {
        return $oldValue === null && $newValue == '';
    }
}
