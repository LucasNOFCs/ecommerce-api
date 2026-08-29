<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get("/", function () {
        return response()->json(["status" => "ok"]);
    });
});
