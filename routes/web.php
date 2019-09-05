<?php

use Illuminate\Support\Facades\Route;

// board
Route::get('/', 'BoardController@index')->name('board.index');

Route::get('/board/create', 'BoardController@create')
    ->middleware('auth')
    ->name('board.create');
Route::get('/board/{id}', 'BoardController@show')->name('board.show');
Route::post('/board', 'BoardController@store')
    ->middleware('auth')
    ->name('board.store');
Route::post('/board/{id}/comment', 'BoardController@storeComment')
    ->middleware('auth')
    ->name('comment.store');

// tag
Route::get('/tag', 'TagController@index')->name('tag.index');
Route::get('/tags/{tag_name}', 'TagController@showBoard')->name('tag.show_board');

// User
Route::get('/user/{user}', 'UserController@show')->name('user.show');

// follow
Route::post('/user/follow/{user}', 'FollowUserController@followUser')
    ->middleware('auth')
    ->name('user.follow');
Route::post('/user/unfollow/{user}', 'FollowUserController@unfollowUser')
    ->middleware('auth')
    ->name('user.unfollow');

// login
Route::get('login/{provider}', 'SocialAccountController@redirectToProvider')
    ->name('social.login');
Route::get('login/{provider}/callback', 'SocialAccountController@handleProviderCallback')
    ->name('social.callback');

// authentication
Auth::routes(['verify' => true]);