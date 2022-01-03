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
    Route::get('/reports/buildings', [\App\Http\Controllers\Reports\BuildingReportController::class, 'show'])->name('reports.buildings');
    Route::get('/reports/itassets', [\App\Http\Controllers\Reports\ItAssetReportController::class, 'show'])->name('reports.itassets');
    Route::get('/reports/supervisors', [\App\Http\Controllers\Reports\SupervisorReportController::class, 'index'])->name('reports.supervisors');
    Route::get('/reports/supervisor/{supervisor}', [\App\Http\Controllers\Reports\SupervisorReportController::class, 'show'])->name('reports.supervisor');
    Route::get('/reports/pending', [\App\Http\Controllers\Reports\PendingReportController::class, 'show'])->name('reports.pending');
    Route::get('/reports/recent', [\App\Http\Controllers\Reports\RecentReportController::class, 'show'])->name('reports.recent');

    Route::get('/building/create', [\App\Http\Controllers\BuildingController::class, 'create'])->name('building.create');
    Route::post('/building/create', [\App\Http\Controllers\BuildingController::class, 'store'])->name('building.store');
    Route::get('/building/{building}', [\App\Http\Controllers\BuildingController::class, 'show'])->name('building.show');
    Route::get('/building/{building}/edit', [\App\Http\Controllers\BuildingController::class, 'edit'])->name('building.edit');
    Route::post('/building/{building}/edit', [\App\Http\Controllers\BuildingController::class, 'update'])->name('building.update');

    Route::get('/building/{building}/room/create', [\App\Http\Controllers\RoomController::class, 'create'])->name('room.create');
    Route::post('/building/{building}/room/create', [\App\Http\Controllers\RoomController::class, 'store'])->name('room.store');
    Route::get('/room/{room}', [\App\Http\Controllers\RoomController::class, 'show'])->name('room.show');
    Route::get('/room/{room}/edit', [\App\Http\Controllers\RoomController::class, 'edit'])->name('room.edit');
    Route::post('/room/{room}', [\App\Http\Controllers\RoomController::class, 'update'])->name('room.update');
    Route::get('/room/{room}/delete', [\App\Http\Controllers\RoomController::class, 'delete'])->name('room.delete');
    Route::post('/room/{room}/delete', [\App\Http\Controllers\RoomController::class, 'destroy'])->name('room.destroy');

    Route::get('/room/{room}/reallocate', [\App\Http\Controllers\RoomReallocationController::class, 'show'])->name('room.reallocate');
    Route::post('/room/{room}/reallocate', [\App\Http\Controllers\RoomReallocationController::class, 'update'])->name('room.do_reallocate');

    Route::get('/room/{room}/email', [\App\Http\Controllers\EmailController::class, 'showRoomForm'])->name('email.room_form');
    Route::post('/room/{room}/email', [\App\Http\Controllers\EmailController::class, 'room'])->name('email.room');
    Route::get('/building/{building}/email', [\App\Http\Controllers\EmailController::class, 'showbuildingForm'])->name('email.building_form');
    Route::post('/building/{building}/email', [\App\Http\Controllers\EmailController::class, 'building'])->name('email.building');

    Route::get('/people/{person}', [\App\Http\Controllers\PeopleController::class, 'show'])->name('people.show');

    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('user.index');

    Route::get('/import/new-requests', [\App\Http\Controllers\ImportNewRequestsController::class, 'create'])->name('import.new_requests_form');
    Route::post('/import/new-requests', [\App\Http\Controllers\ImportNewRequestsController::class, 'store'])->name('import.new_requests');
});

require __DIR__.'/auth.php';
