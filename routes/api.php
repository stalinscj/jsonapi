<?php

use CloudCreativity\LaravelJsonApi\Facades\JsonApi;
use CloudCreativity\LaravelJsonApi\Routing\RouteRegistrar;
use CloudCreativity\LaravelJsonApi\Routing\RelationshipsRegistration;

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

    $api->resource('articles')
        ->relationships(function (RelationshipsRegistration $api) {
            $api->hasOne('authors')->except('replace');
        });

    $api->resource('authors')->only('index', 'read');
        
    $api->resource('categories')->only('index', 'read');
    
});