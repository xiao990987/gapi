<?php
namespace app;
use gapi\Route;

Route::get(['/index'],'home/Index/index',[]);
Route::get(['/next'],'home/Index/next',[]);
Route::get(['/num/{num}/{str}'],'home/Index/nums',['num'=>'\d+','str'=>'.+']);
Route::get(['/hello'],'home/Index/hello',[]);
Route::get(['/index3'],'home/Index3/index',[]);
