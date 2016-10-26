## Security Employee Tracker

[![Build Status](https://travis-ci.org/scci/security-employee-tracker.svg?branch=master)](https://travis-ci.org/scci/security-employee-tracker)

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

1. Upload your files to your server so that the public directory is what the browser loads when accessing the application.
2. Utilize a .env file or edit the various files in the config folder to setup your application settings. The .env file will take presidence.
  * Update the `config/adldap.php` file to reflect your LDAP settings. 
3. In your command line/SSH, navigate to the application root folder and run: `composer install`
4. Generate a secure application key: `php artisan key:generate`. Ensure that this key is added to your .env or your `config/app.php` file.
5. Edit `config/auth.php` and add your username to the `admin` section. Also change the `guards => web => provider` (line 39) value to `adldap` (currently set to users for testing)
6. Load the application in your browser.

## Tests

You may run tests via executing the following in the application's root folder: `phpunit`

## Contribute

If you wish to submit enhancements, bug fixes and other changes, please submit a pull request. Pull request must have all changes for a single feature **and test cases**.

### License

The SET application is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Current main tasks

- Implement ability to create users and reset passwords via the users table and not require LDAP
- Finish adding all missing test cases.
