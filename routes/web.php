<?php

use Illuminate\Support\Facades\Route;

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


Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [\App\Http\Controllers\HomeController::class, 'show'])->name('home');
    Route::get('/reports/people', [\App\Http\Controllers\Reports\PeopleReportController::class, 'show'])->name('reports.people');

    Route::get('/building/create', [\App\Http\Controllers\BuildingController::class, 'create'])->name('building.create');
    Route::post('/building/create', [\App\Http\Controllers\BuildingController::class, 'store'])->name('building.store');
    Route::get('/building/{building}', [\App\Http\Controllers\BuildingController::class, 'show'])->name('building.show');
    Route::get('/building/{building}/edit', [\App\Http\Controllers\BuildingController::class, 'edit'])->name('building.edit');
    Route::post('/building/{building}/edit', [\App\Http\Controllers\BuildingController::class, 'update'])->name('building.update');

    Route::get('/room/{room}', [\App\Http\Controllers\RoomController::class, 'edit'])->name('room.edit');
    Route::post('/room/{room}', [\App\Http\Controllers\RoomController::class, 'update'])->name('room.update');
});

require __DIR__.'/auth.php';
