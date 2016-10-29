## Security Employee Tracker

[![MIT Licence](https://badges.frapsoft.com/os/mit/mit.svg?v=103)](https://opensource.org/licenses/mit-license.php)   [![Build Status](https://travis-ci.org/scci/security-employee-tracker.svg?branch=master)](https://travis-ci.org/scci/security-employee-tracker) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/7a8de56d-c4d4-492f-8ea3-d7f8a2441bad/mini.png)](https://insight.sensiolabs.com/projects/7a8de56d-c4d4-492f-8ea3-d7f8a2441bad) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/scci/security-employee-tracker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/scci/security-employee-tracker/?branch=master) [![Dependency Status](https://www.versioneye.com/user/projects/58123c993130eb0043c41242/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/58123c993130eb0043c41242)

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
2. Set your domain to point to the `public` directory.
3. From SSH, execute `composer install`.
4. Navigate to your site/install. IE: http://set.company.com/install
5. Follow the on screen prompts. (Default env settings should function for you).
6. Recommended/Optional: From SSH, execute `php artisan generate:key` - while you already have a secure encryption key, if you wish to have one unique to you (heightened security), then run this.

## Updating

* Execute `git pull`. 

## FAQs

**Error Installing Database**

1. Change the PHP max execution time to a higher value such as 60 seconds.
2. Set the .env file to `APP_ENV=local` until after the installation is complete.
3. From SSH, execute `php artisan migrate --force` and then go to your URL/install/user. IE: http://set.company.com/install/user
4. Review the log contents: /storage/logs/laravel.log

**Using LDAP**

* `config/adldap.php` - input your ldap credentials. This file should have enough comments to help you.
* `config/auth.php` - look for the array guards => web => providers and change the value on line 39 to `adldap`.

**Hard coding Admins**

* `config/auth.php` - line 111, set each username, you may have multiples. 

**Additional Customization**

* Review each file in the config directory. This application is also built on the [Laravel](https://laravel.com/docs/master/) framework. You may also review their documentation for additional settings to use.

## Tests

You may run tests via executing the following in the application's root folder: `phpunit`

## Contribute

If you wish to submit enhancements, bug fixes and other changes, please submit a pull request. Pull request must have all changes for a single feature **and test cases**.