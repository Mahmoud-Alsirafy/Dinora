<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'hello';
});

require __DIR__ . '/auth.php';