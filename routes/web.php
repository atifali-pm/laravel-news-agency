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

Route::get('/', function() {
    return redirect('stories');
});

Route::patch('stories/{story}/update', 'StoriesController@update');
Route::post('stories', 'StoriesController@store');
Route::delete('stories/{story}/destroy', 'StoriesController@destroy');
Route::get('stories', 'StoriesController@index');
Route::get('stories/{story}/show', 'StoriesController@show');

Route::post('stories/{story}/comments', 'StoryCommentsController@store');
