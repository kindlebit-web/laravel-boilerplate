<?php
Route::get('/', function () {
    return view('welcome');
});

// sdad

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('activate_deactivate_users/{user_id}', 'AdminController@toggleUserActivateStatus')->name('activate_deactivate');
});


Route::group(['middleware' => ['admin.user']], function(){

  Route::get('generator_builder', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@builder');

  Route::get('field_template', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@fieldTemplate');

  Route::get('relation_field_template', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@relationFieldTemplate');

  Route::post('generator_builder/generate', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@generate');

  Route::post('generator_builder/rollback', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@rollback');

  Route::post(
      'generator_builder/generate-from-file',
      '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@generateFromFile'
  );

});

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');
