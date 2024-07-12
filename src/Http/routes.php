<?php

use ManoCode\AiRoles\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('ai_roles')->group(function () {
    Route::resource('list', Controllers\AiRoleController::class);
    Route::resource('cate', Controllers\AiRolesCateController::class);
    Route::get('test', [Controllers\AiRoleController::class ,'aigen']);
//    Route::get('demo',[\ManoCode\AiRoles\Library\QwenStream::class,'qwenChat'])->withoutMiddleware('admin.auth')->withoutMiddleware('admin.permission');;
});
//Route::resource('/ai-roles/list', Controllers\AiRoleController::class);
//Route::get('ai-roles', [Controllers\AiRolesController::class, 'index']);

