<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::loginUsingId(1);

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::apiResource('/api/team', 'Api\TeamController');

Route::resource('/api/team/{team}/sprint', 'Api\SprintController')->only(['index', 'store']);
Route::resource('/api/sprint', 'Api\SprintController')->only(['update', 'destroy', 'show']);

Route::resource('/api/sprint/{sprint}/story', 'Api\StoryController')->only(['index', 'store']);
Route::resource('/api/story', 'Api\StoryController')->only(['update', 'destroy', 'show']);

Route::get('/api/task/state', 'Api\TaskStateController@index')->name('state.index');

Route::resource('/api/story/{story}/task', 'Api\TaskController')->only(['index', 'store']);
Route::resource('/api/task', 'Api\TaskController')->only(['update', 'destroy', 'show']);

