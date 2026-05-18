<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use Illuminate\Http\Request;
use App\Events\CursorMoved;

Route::get(

    '/dashboard',

    function () {

        return redirect('/');

    }

)->middleware(
    ['auth', 'verified']
)->name('dashboard');

Route::middleware(
    'auth'
)->group(function () {

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');

    /* COLLABORATIVE EDITOR */

    Route::get(
        '/',
        [DocumentController::class, 'index']
    );

    Route::post(
        '/autosave',
        [DocumentController::class, 'autosave']
    );

    /* DOCUMENT REVISION */

    Route::post(
        '/revision/{id}/restore',
        [DocumentController::class, 'restore']
    );

    /* LIVE CURSOR TRACKING */
Route::post(

    '/cursor',

    function () {

        broadcast(

            new CursorMoved(
                0,
                0
            )

        )->toOthers();

        return response()->json([

            'success' => true

        ]);

    }

);

});

require __DIR__.'/auth.php';