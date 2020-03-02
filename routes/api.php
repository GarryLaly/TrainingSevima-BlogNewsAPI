<?php

Route::post('register', 'RegisterController@action');
Route::post('login', 'LoginController@action');

Route::get('forbidden', 'NewsController@forbidden')->name('api.forbidden');
Route::get('news', 'NewsController@index')->name('api.news');
Route::get('news/{news}', 'NewsController@show');

Route::middleware('auth:api')->group(function() {
    Route::post('news', 'NewsController@store');
    Route::post('news/{id}', 'NewsController@update');
    Route::delete('news/{id}', 'NewsController@destroy');
    Route::get('news-by-user', 'NewsController@indexByUser');
});