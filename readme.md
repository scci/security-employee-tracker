## Security Employee Tracker

[![MIT Licence](https://badges.frapsoft.com/os/mit/mit.svg?v=103)](https://opensource.org/licenses/mit-license.php)   [![Build Status](https://travis-ci.org/scci/security-employee-tracker.svg?branch=master)](https://travis-ci.org/scci/security-employee-tracker) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/7a8de56d-c4d4-492f-8ea3-d7f8a2441bad/mini.png)](https://insight.sensiolabs.com/projects/7a8de56d-c4d4-492f-8ea3-d7f8a2441bad) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/scci/security-employee-tracker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/scci/security-employee-tracker/?branch=master) [![Dependency Status](https://www.versioneye.com/user/projects/58123c993130eb0043c41242/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/58123c993130eb0043c41242) [![StyleCI](https://styleci.io/repos/72014517/shield?branch=master)](https://styleci.io/repos/72014517)

SET is for FSO and security officers of DoD companies to manage their employees. This includes the following:
- storing employee files
- tracking training (including renewal requirements)
- monitoring security clearance
- handling visitation rights to bases
- ensuring employees are prepared for travel
- storing miscellaneous notes.
- tracking employee security checks/duty roster that changes on a monthly, weekly or daily basis
- mass notification of news
- import of JPAS security clearance and investigation dates.

## Server Requirements

The application currently utilizes the PHP Laravel 5 framework. It also currently requires an LDAP connection.

- PHP >= 5.6.4
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- LDAP PHP Extension
- [Composer](https://getcomposer.org/)

To view all the various options including cache, database, settings, email, etc..., view the [Laravel Documentation.](https://laravel.com/docs/master)

## Installation

1. From SSH, execute `git clone https://github.com/scci/security-employee-tracker.git` in your web root directory.
2. Set your domain to point to the `security-employee-tracker/public` directory.
3. From SSH, execute `composer install` from within the `security-employee-tracker` directory.
4. Navigate to your site/install. IE: http://set.company.com/install or http://localhost/security-employee-tracker/public/install
  1. Follow the on screen prompts. 
  2. Update your .env file as needed.
  3. Check for required extensions and permissions.
  4. Perform the database installation (this may take some time).
  5. Create Admin User.
  6. Note: Install page will only be available upon initialization.
6. Create a cron job/scheduled task: `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1` (artisan is located in the application root directory.)
7. From SSH, execute `php artisan generate:key` to have one unique to you (heightened security).

## Updating

* Execute `git pull`.
* If there was a database update/new migration files, execute `php artisan migrate --force`

## FAQs

1. Change the PHP max execution time to a higher value such as 60 seconds.
2. From SSH, execute `php artisan migrate --force` and then go to your URL/install/user. IE: http://set.company.com/install/user
3. Review the log contents: /storage/logs/laravel.log

**Using LDAP (After Install)**

* `config/adldap.php` - input your ldap credentials. This file should have enough comments to help you.
* Insert a new auth driver inside your `config/auth.php` file:

    ```php
    'providers' => [
        'users' => [
            'driver' => 'adldap', // Was 'eloquent'.
            'model'  => App\User::class,
        ],
    ],
    ```
* For more customizing options, visit: https://github.com/Adldap2/Adldap2-Laravel

NOTE: The initial login after setting up ldap will take some time as your users are imported into SET.

**Hard coding Admins**

* `config/auth.php` - line 111, set each username, you may have multiples. 

**Additional Customization**

* Review each file in the config directory. This application is also built on the [Laravel](https://laravel.com/docs/master/) framework. You may also review their documentation for additional settings to use.

**What do I do on each page?**

* At the top of each page is a clickable question mark. Click that and the page help will display explaining how to utilize the page.

## Tests

You may run tests via executing the following in the application's root folder: `phpunit`. While testing, you may place the app in debug move by editing the .env file and setting `APP_DEBUG = true`.

## Contribute

If you wish to submit enhancements, bug fixes and other changes, please submit a pull request. Pull request must have all changes for a single feature **and test cases**.
