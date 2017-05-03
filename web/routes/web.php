<?php

//
Route::get('/',      'PostController@index');
Route::get('/home',  'PostController@index');
Route::get('/home/{slug}',  'PostController@index');

//
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/login', 'Auth\LoginController@index');
Route::post('/login', 'Auth\LoginController@login');

//
Route::get('/register', 'Auth\RegisterController@index');
Route::post('/register', 'Auth\RegisterController@register');

//
Route::get('view/{slug}','PostController@view');

//
Route::group(['middleware' => ['auth']], function()
{
	Route::get('edit/{slug}', 'PostController@edit');
	Route::post('edit/{slug}', 'PostController@update');
	
	Route::get('/write',  'PostController@write');
	Route::post('/write', 'PostController@writePost');
	
	Route::get('delete/{id}','PostController@destroy');
});
