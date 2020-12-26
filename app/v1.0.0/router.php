<?php
namespace app;


use gapi\Route;

Route::get(['/index.html'],'home/index/index',[]);
Route::get(['/index','/'],'home/index/index',[]);
Route::get(['/next'],'home/index/next',[]);
Route::get(['/num/{num}/{str}.html','/num/{num}/{str}'],'home/index/nums',['num'=>'\d+','str'=>'\w+']);