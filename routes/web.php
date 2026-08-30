<?php

use Illuminate\Support\Facades\Route;

Route::get("/up", function () {
    return response()->json(
        [
            "message" => "Application is running ok.",
            "data" => null,
        ],
        200,
    );
});

Route::get("/", function () {
    return response()->json(["status" => "Ok"]);
});
