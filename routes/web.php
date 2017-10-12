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

use App\Sheep;

Route::get('/', 'HomeController@welcome');

Route::get('/reproduce/', 'HomeController@reproduce');

Route::get('/sleep/', 'HomeController@sleep');

Route::get('/stat/', 'HomeController@stat');

Route::get('/reset/', function () {
	Sheep::reset();
});