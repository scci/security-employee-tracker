## Security Employee Tracker

[![MIT Licence](https://badges.frapsoft.com/os/mit/mit.svg?v=103)](https://opensource.org/licenses/mit-license.php)   [![Build Status](https://travis-ci.org/scci/security-employee-tracker.svg?branch=master)](https://travis-ci.org/scci/security-employee-tracker) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/7a8de56d-c4d4-492f-8ea3-d7f8a2441bad/mini.png)](https://insight.sensiolabs.com/projects/7a8de56d-c4d4-492f-8ea3-d7f8a2441bad) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/scci/security-employee-tracker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/scci/security-employee-tracker/?branch=master) [![Dependency Status](https://www.versioneye.com/user/projects/58123c993130eb0043c41242/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/58123c993130eb0043c41242) [![StyleCI](https://styleci.io/repos/72014517/shield?branch=master)](https://styleci.io/repos/72014517)

SET is for FSO and security officers of DoD companies to manage their employees. This includes storing the following:
- employee files
- training and briefings
- security clearance
- visitation rights to bases
- employee travel plans
- miscellaneous notes
- employee security checks/duty roster
- newsletters
- import of JPAS

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
6. Create a cron job/scheduled task: `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1` (artisan is located in the application root directory.)
7. From SSH, execute `php artisan generate:key` to have one unique to you (heightened security).

_Note: Install page will only be available upon initialization._

## Updating

* Execute `git pull`.
* If there was a database update/new migration files, execute `php artisan migrate --force`

## Contribute

If you wish to submit enhancements, bug fixes and other changes, please submit a pull request. Pull request must have all changes for a single feature **and test cases**.

## More Information

Want more information about the application? [View the Wiki](https://github.com/scci/security-employee-tracker/wiki/FAQs).