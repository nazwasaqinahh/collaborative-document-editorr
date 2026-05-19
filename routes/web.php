<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use Illuminate\Http\Request;

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

    /* PROFILE */

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

    /* DOCUMENT LIST */

    Route::get(

        '/',

        function(){

            return redirect(
                '/documents'
            );

        }

    );

    Route::get(

        '/documents',

        [DocumentController::class, 'documents']

    );

    /* CREATE DOCUMENT */

    Route::get(

        '/documents/create',

        [DocumentController::class, 'create']

    );

    Route::post(

        '/documents/store',

        [DocumentController::class, 'store']

    );

    /* OPEN DOCUMENT */

    Route::get(

        '/documents/{id}',

        [DocumentController::class, 'show']

    );

    /* AUTOSAVE */

    Route::post(

        '/documents/{id}/autosave',

        [DocumentController::class, 'autosave']

    );

    /* DELETE DOCUMENT */

    Route::delete(

        '/documents/{id}',

        [DocumentController::class, 'destroy']

    );

    /* DOCUMENT REVISION */

    Route::post(

        '/revision/{id}/restore',

        [DocumentController::class, 'restore']

    );

});

require __DIR__.'/auth.php';