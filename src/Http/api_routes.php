<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'/airole'],function(){
    // 获取角色列表
    Route::get('/get-role-lists',[\ManoCode\AiRoles\Http\Controllers\Api\APIController::class,'getRoleLists']);
    // chat
    Route::get('/chat',[\ManoCode\AiRoles\Http\Controllers\Api\APIController::class,'chat']);
});


