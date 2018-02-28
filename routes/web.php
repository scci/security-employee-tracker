<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();

Route::group(['middleware' => 'canInstall', 'namespace' => 'Installation'], function () {
    Route::resource('install/user', 'AdminController');
    Route::resource('install/environment', 'EnvironmentController');
});

// This group requires a user to be logged in.
Route::group(['middleware' => 'auth'], function () {

    //when you login, if you were not going to a page, you get directed to /home.
    // Let's push that to the actual home page.
    Route::get('home', function () {
        return redirect('/');
    });

    Route::get('/', 'HomeController@index');
    Route::get('/search', 'HomeController@search');
    Route::get('training/{trainingID}/assign', ['as' => 'training.assignForm', 'uses' => 'TrainingController@assignForm']);
    Route::post('training/{trainingID}/assign', ['as' => 'training.assign', 'uses' => 'TrainingController@assign']);
    Route::get('training/{trainingID}/bulkupdate', ['as' => 'training.updateForm', 'uses' => 'TrainingController@updateForm']);
    Route::post('training/{trainingID}/bulkupdate', ['as' => 'training.bulkupdate', 'uses' => 'TrainingController@bulkupdate']);
    Route::get('user/{userID}/{sectionID}/show', ['uses' => 'UserController@show']);
    Route::get('/user/status/{userStatus}', ['uses' => 'UserController@index']);
    Route::get('/training/completed', ['uses' => 'TrainingController@showCompleted']);
    // Pass the Training Type to the Training index
    Route::get('/training/trainingtype/{trainingTypeID}', ['uses' => 'TrainingController@index']);
    Route::get('/training/reminder/{noteID}', ['uses' => 'TrainingController@sendReminder']);
    Route::resource('user', 'UserController');
    Route::post('user-import', ['as' => 'user.import', 'uses' => 'UserController@import']);
    Route::post('resolve-import', ['as' => 'user.resolveImport', 'uses' => 'UserController@resolveImport']);
    Route::resource('user.note', 'NoteController');
    Route::resource('user.training', 'TrainingUserController');
    Route::resource('user.visit', 'VisitController');
    Route::resource('user.travel', 'TravelController');
    Route::resource('training', 'TrainingController');
    Route::resource('trainingtype', 'TrainingTypeController');
    Route::resource('attachment', 'AttachmentController');
    Route::resource('group', 'GroupController');
    Route::post('group-user-id', 'GroupController@getUserIDs');
    Route::resource('settings', 'SettingController');
    Route::resource('duty', 'DutyController');
    Route::resource('duty-swap', 'DutySwapController');
    Route::resource('news', 'NewsController');
    Route::get('logout', 'Auth\LoginController@logout');
});
