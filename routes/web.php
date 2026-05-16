<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Events\DocumentUpdated;

/* SHOW DOCUMENT */

Route::get(
    '/documents/{document}',

    function (Document $document) {

        return view(
            'documents.show',
            compact('document')
        );

    }
);

/* AUTOSAVE */

Route::post(
    '/documents/{document}/autosave',

    function (
        Request $request,
        Document $document
    ) {

        $document->update([

            'title' =>
                $request->title,

            'content' =>
                $request->content

        ]);

        broadcast(

            new DocumentUpdated(
                $document->fresh()
            )

        )->toOthers();

        return response()->json([
            'success' => true
        ]);

    }
);