<?php

use CloudCreativity\LaravelJsonApi\Facades\JsonApi;
use CloudCreativity\LaravelJsonApi\Routing\RouteRegistrar;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

JsonApi::register('v1')->routes(function (RouteRegistrar $api) {

    $api->resource('articles')->only('create', 'update', 'delete')->middleware('auth');
    $api->resource('articles')->except('create', 'update', 'delete');
    
});