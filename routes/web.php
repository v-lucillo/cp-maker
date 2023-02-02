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

Route::get('/', "GenerateController@index")->name("main");
Route::get('/preview', "GenerateController@preview")->name("preview");
Route::post('/upload_cp', "GenerateController@upload_cp")->name("upload_cp");
Route::post('/invoke_cp', "GenerateController@invoke_cp")->name("invoke_cp");


Route::get('/generate_cp', "GenerateController@generate_cp")->name("generate_cp");
Route::get('/get_generated_files', "GenerateController@get_generated_files")->name("get_generated_files");


