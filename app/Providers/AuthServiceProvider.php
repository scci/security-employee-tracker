<?php

namespace SET\Providers;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
use SET\Note;

/**
 * Class AuthServiceProvider
 * @package SET\Providers
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'SET\Model' => 'SET\Policies\ModelPolicy',
    ];


    /**
     * @param GateContract $gate
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        //user can edit this. AKA, they are an admin.
        $gate->define('edit', function ($user) {
            return $this->isAdmin($user);
        });

        //user has view rights
        $gate->define('view', function ($user) {
            return $this->isViewer($user);
        });

        //Let admin update note & user update training note.
        $gate->define('update_record', function ($user, $record) {
            return ($user->id == $record->user_id || $this->isAdmin($user));
        });

        // primarily used to set javascript variable.
        $gate->define('update_self', function ($user, $page) {
            return $user->id == $page->id && !$this->isAdmin($user);
        });

        $gate->define('show_user', function ($user, $page) {
            return ($this->isViewer($user) || $user->id === $page->id);
        });

        $gate->define('show_note', function ($user, $page) {
            return ($this->isAdmin($user) || Note::findOrFail($page->id)->user()->id === $user->id);
        });
        
        $gate->define('show_published_news', function ($user, $news) {
            return ($this->isViewer($user) || 
                    ($news->publish_date <= Carbon::today() &&
                      ($news->expire_date >= Carbon::today() || 
                        is_null($news->expire_date))));
        });

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Check if we have defined the user as an admin in their record on in the config file.
     * @param $user
     * @return bool
     */
    private function isAdmin($user)
    {
        return $user->role == 'edit' || in_array($user->username, Config::get('auth.admin'));
    }

    /**
     * See if we have set the role to view or higher.
     * @param $user
     * @return bool
     */
    private function isViewer($user)
    {
        return $user->role == 'view' || $this->isAdmin($user);
    }
}
