<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('authenticated/Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/roles', function () {
        // Uses first & second middleware...
        return Inertia::render('authenticated/Roles');
    })->name('roles');
 
    Route::get('/users', function () {
        // Uses first & second middleware...
        return Inertia::render('authenticated/Users');
    })->name('users'); 

    Route::get('/expenses', function () {
        // Matches The "/admin/users" URL
        return Inertia::render('authenticated/Expenses');
    })->name('expenses');

    Route::get('/categories', function () {
        // Matches The "/admin/users" URL
        return Inertia::render('authenticated/Categories');
    })->name('categories');


    /*
        We can put this ont he api.php  routes and add Guards or token 
        but since no part of this will be available outside we will put it here
    */    

    Route::apiResource('/emgr/roles',\App\Http\Controllers\RolesController::class);
    Route::apiResource('/emgr/users',\App\Http\Controllers\UserController::class);
    Route::apiResource('/emgr/expensescat',\App\Http\Controllers\ExpensesCategory::class);
    Route::apiResource('/emgr/expenses',\App\Http\Controllers\ExpensesController::class);    

    Route::get('/myexpenses',function(){
        return \App\Models\ExpensesModel::Where('user_id',Auth::id())
        ->selectRaw("SUM(amount) as amount, expense_category_id")
        ->groupBy('expense_category_id')
        ->with(['category' => function($query){
            $query->select('category_name as category','id');
        }])
        ->get();
    });
});

require __DIR__.'/auth.php';
