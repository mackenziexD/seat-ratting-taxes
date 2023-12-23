<?php

Route::group([

    'namespace' => 'Helious\SeatRattingTaxes\Http\Controllers',
    'prefix' => 'ratting-taxes',
    'middleware' => [
        'web',
        'auth',
        'can:seat-ratting-taxes.access',
    ],
], function()
{

    Route::get('/', [
        'uses' => 'RattingTaxController@index',
        'as' => 'seat-ratting-taxes::index',
    ]);

});