<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
// all posts, 15 per page
Route::get('posts', 'PostController@getPosts');

// get single post
Route::get('posts/{id}', 'PostController@getPost');

// create new post
Route::post('posts', 'PostController@createPost');

// update a post
Route::post('posts/{id}', 'PostController@updatePost');

// delete a post
Route::post('posts', 'PostController@delete');