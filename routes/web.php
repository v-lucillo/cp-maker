<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', "LoginController@login")->name("login");

Route::get('main', "GenerateController@main")->name("main")->middleware('access_guard');
Route::get('/preview', "GenerateController@preview")->name("preview")->middleware('access_guard');
Route::post('/upload_cp', "GenerateController@upload_cp")->name("upload_cp")->middleware('access_guard');
Route::post('/invoke_cp', "GenerateController@invoke_cp")->name("invoke_cp")->middleware('access_guard');


Route::post('/sign_in', "LoginController@sign_in")->name("sign_in");


Route::get('/generate_cp', "GenerateController@generate_cp")->name("generate_cp")->middleware('access_guard');
Route::get('/get_generated_files', "GenerateController@get_generated_files")->name("get_generated_files")->middleware('access_guard');


Route::get('/logout', function(){
	session()->flush();
	return redirect()->route("login");
})->name("logout");


Route::get('/set_preview_format', "GenerateController@set_preview_format")->name("set_preview_format")->middleware('access_guard');

Route::get('/g_drive_upload', "GenerateController@g_drive_upload")->name("g_drive_upload")->middleware('access_guard');

