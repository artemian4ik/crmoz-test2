<?php

use Illuminate\Support\Facades\Route;

Route::get('/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

