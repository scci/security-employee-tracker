<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Connection
    |--------------------------------------------------------------------------
    |
    | The LDAP connection to use for laravel authentication.
    |
    | You must specify connections in your `config/adldap.php` configuration file.
    |
    | This must be a string.
    |
    */

    'connection' => env('LDAP_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Provider
    |--------------------------------------------------------------------------
    |
    | The LDAP authentication provider to use depending
    | if you require database synchronization.
    |
    | For synchronizing LDAP users to your local applications database, use the provider:
    |
    | Adldap\Laravel\Auth\DatabaseUserProvider::class
    |
    | Otherwise, if you just require LDAP authentication, use the provider:
    |
    | Adldap\Laravel\Auth\NoDatabaseUserProvider::class
    |
    */

    'provider' => Adldap\Laravel\Auth\DatabaseUserProvider::class,

    /*
    |--------------------------------------------------------------------------
    | Model
    |--------------------------------------------------------------------------
    |
    | The model to utilize for authentication and importing.
    |
    | This option is only applicable to the DatabaseUserProvider.
    |
    */

    'model' => SET\User::class,

    /*
    |--------------------------------------------------------------------------
    | Rules
    |--------------------------------------------------------------------------
    |
    | Rules allow you to control user authentication requests depending on scenarios.
    |
    | You can create your own rules and insert them here.
    |
    | All rules must extend from the following class:
    |
    |   Adldap\Laravel\Validation\Rules\Rule
    |
    */

    'rules' => [

        // Denys deleted users from authenticating.

        Adldap\Laravel\Validation\Rules\DenyTrashed::class,

        // Allows only manually imported users to authenticate.

        // Adldap\Laravel\Validation\Rules\OnlyImported::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes allow you to restrict the LDAP query that locates
    | users upon import and authentication.
    |
    | All scopes must implement the following interface:
    |
    |   Adldap\Laravel\Scopes\ScopeInterface
    |
    */

    'scopes' => [

        // Only allows users with a user principal name to authenticate.
        // Suitable when using ActiveDirectory.
        Adldap\Laravel\Scopes\UpnScope::class,
        SET\Scopes\GroupScope::class,

        // Only allows users with a uid to authenticate.
        // Suitable when using OpenLDAP.
        // Adldap\Laravel\Scopes\UidScope::class,

    ],

    'usernames' => [

        /*
        |--------------------------------------------------------------------------
        | LDAP
        |--------------------------------------------------------------------------
        |
        | Discover:
        |
        |   The discover value is the users attribute you would
        |   like to locate LDAP users by in your directory.
        |
        |   For example, using the default configuration below, if you're
        |   authenticating users with an email address, your LDAP server
        |   will be queried for a user with the a `userprincipalname`
        |   equal to the entered email address.
        |
        | Authenticate:
        |
        |   The authenticate value is the users attribute you would
        |   like to use to bind to your LDAP server.
        |
        |   For example, when a user is located by the above 'discover'
        |   attribute, the users attribute you specify below will
        |   be used as the username to bind to your LDAP server.
        |
        */

        'ldap' => [

            'discover' => 'samaccountname',

            'authenticate' => 'samaccountname',

        ],

        /*
        |--------------------------------------------------------------------------
        | Eloquent
        |--------------------------------------------------------------------------
        |
        | The value you enter is the database column name used for locating
        | the local database record of the authenticating user.
        |
        | If you're using a `username` column instead, change this to `username`.
        |
        | This option is only applicable to the DatabaseUserProvider.
        |
        */

        'eloquent' => 'username',

        /*
        |--------------------------------------------------------------------------
        | Windows Authentication Middleware (SSO)
        |--------------------------------------------------------------------------
        |
        | Discover:
        |
        |   The 'discover' value is the users attribute you would
        |   like to locate LDAP users by in your directory.
        |
        |   For example, if 'samaccountname' is the value, then your LDAP server is
        |   queried for a user with the 'samaccountname' equal to the value of
        |   $_SERVER['AUTH_USER'].
        |
        |   If a user is found, they are imported (if using the DatabaseUserProvider)
        |   into your local database, then logged in.
        |
        | Key:
        |
        |    The 'key' value represents the 'key' of the $_SERVER
        |    array to pull the users account name from.
        |
        |    For example, $_SERVER['AUTH_USER'].
        |
        */

        'windows' => [

            'discover' => 'samaccountname',

            'key' => 'AUTH_USER',

        ],

    ],

    'passwords' => [

        /*
        |--------------------------------------------------------------------------
        | Password Sync
        |--------------------------------------------------------------------------
        |
        | The password sync option allows you to automatically synchronize users
        | LDAP passwords to your local database. These passwords are hashed
        | natively by Laravel using the Hash::make() method.
        |
        | Enabling this option would also allow users to login to their accounts
        | using the password last used when an LDAP connection was present.
        |
        | If this option is disabled, the local database account is applied a
        | random 16 character hashed password upon first login, and will
        | lose access to this account upon loss of LDAP connectivity.
        |
        | This option must be true or false and is only applicable
        | to the DatabaseUserProvider.
        |
        */

        'sync' => env('LDAP_PASSWORD_SYNC', false),

        /*
        |--------------------------------------------------------------------------
        | Column
        |--------------------------------------------------------------------------
        |
        | This is the column of your users database table
        | that is used to store passwords.
        |
        | Set this to `null` if you do not have a password column.
        |
        | This option is only applicable to the DatabaseUserProvider.
        |
        */

        'column' => 'password',

    ],

    /*
    |--------------------------------------------------------------------------
    | Login Fallback
    |--------------------------------------------------------------------------
    |
    | The login fallback option allows you to login as a user located on the
    | local database if active directory authentication fails.
    |
    | Set this to true if you would like to enable it.
    |
    | This option must be true or false and is only
    | applicable to the DatabaseUserProvider.
    |
    */

    'login_fallback' => env('LDAP_LOGIN_FALLBACK', true),

    /*
    |--------------------------------------------------------------------------
    | Sync Attributes
    |--------------------------------------------------------------------------
    |
    | Attributes specified here will be added / replaced on the user model
    | upon login, automatically synchronizing and keeping the attributes
    | up to date.
    |
    | The array key represents the users Laravel model key, and
    | the value represents the users LDAP attribute.
    |
    | You **must** include the users login attribute here.
    |
    | This option must be an array and is only applicable
    | to the DatabaseUserProvider.
    |
    */

    'sync_attributes' => [

        'email' => 'samaccountname',

        'name'       => 'cn',
        'first_name' => 'givenname',
        'last_name'  => 'sn',
        'email'      => 'mail',
        'phone'      => 'telephonenumber',

    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | User authentication attempts will be logged using Laravel's
    | default logger if this setting is enabled.
    |
    | No credentials are logged, only usernames.
    |
    | This is usually stored in the '/storage/logs' directory
    | in the root of your application.
    |
    | This option is useful for debugging as well as auditing.
    |
    | You can freely remove any events you would not like to log below,
    | as well as use your own listeners if you would prefer.
    |
    */

    'logging' => [

        'enabled' => true,

        'events' => [

            \Adldap\Laravel\Events\Importing::class                 => \Adldap\Laravel\Listeners\LogImport::class,
            \Adldap\Laravel\Events\Synchronized::class              => \Adldap\Laravel\Listeners\LogSynchronized::class,
            \Adldap\Laravel\Events\Synchronizing::class             => \Adldap\Laravel\Listeners\LogSynchronizing::class,
            \Adldap\Laravel\Events\Authenticated::class             => \Adldap\Laravel\Listeners\LogAuthenticated::class,
            \Adldap\Laravel\Events\Authenticating::class            => \Adldap\Laravel\Listeners\LogAuthentication::class,
            \Adldap\Laravel\Events\AuthenticationFailed::class      => \Adldap\Laravel\Listeners\LogAuthenticationFailure::class,
            \Adldap\Laravel\Events\AuthenticationRejected::class    => \Adldap\Laravel\Listeners\LogAuthenticationRejection::class,
            \Adldap\Laravel\Events\AuthenticationSuccessful::class  => \Adldap\Laravel\Listeners\LogAuthenticationSuccess::class,
            \Adldap\Laravel\Events\DiscoveredWithCredentials::class => \Adldap\Laravel\Listeners\LogDiscovery::class,
            \Adldap\Laravel\Events\AuthenticatedWithWindows::class  => \Adldap\Laravel\Listeners\LogWindowsAuth::class,
            \Adldap\Laravel\Events\AuthenticatedModelTrashed::class => \Adldap\Laravel\Listeners\LogTrashedModel::class,

        ],
    ],

];
