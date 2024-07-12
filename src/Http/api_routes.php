<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'/airole'],function(){
    Route::get('demo',[\ManoCode\AiRoles\Library\QwenStream::class,'qwenChat']);
});


