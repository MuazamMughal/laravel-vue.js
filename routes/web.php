<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/new', function () 
//         {
//             return view('new');
//     });

// Route::get('/new/{name}', function($name) {
//     echo $name;
//     // now e passing name directly to the blade page 
//     return view('dynamic' ,[ 'name' => $name ]);
// });
// // here we lern the rredirecting to any url 
// Route::redirect('/home', '/');



// so here im gona write the routes for the user controller
Route::get('/', [UserController::class ,'getUser'])->name('getUser');

Route::get('/new', [UserController::class ,'aboutUser2'])->name('aboutUser2');

Route::get('/new/{name}', [UserController::class ,'getUserName'])->name('getUserName');  

Route::get('/view/{name}', [UserController::class ,'view'])->name('view');

// now i am gona study the view afther the controler basics
