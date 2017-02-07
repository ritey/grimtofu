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

Route::get('/',['as' => 'home', 'uses' => 'HomeController@index']);
Route::get('/login',['as' => 'github_link', 'uses' => 'HomeController@login']);
Route::get('/logout',['as' => 'clear-session', 'uses' => 'HomeController@logout']);
Route::get('/help',['as' => 'help', 'uses' => 'PageController@help']);

Route::get('/index', ['as' => 'index', 'uses' => 'PageController@index']);
Route::get('/new', ['as' => 'new', 'uses' => 'PageController@newThread']);
Route::post('/new', ['as' => 'save.thread', 'uses' => 'PageController@saveThread']);
Route::post('/comment', ['as' => 'save.comment', 'uses' => 'PageController@saveComment']);
Route::get('/channels/{channel}', ['as' => 'channel', 'uses' => 'PageController@channel']);
Route::get('/channels/{channel}/{title}', ['as' => 'thread', 'uses' => 'PageController@thread']);
Route::get('/u/{username}', ['as' => 'user', 'uses' => 'PageController@user']);

Route::get('/oauth/callback',['as' => 'callback', 'uses' => 'GithubController@callback']);