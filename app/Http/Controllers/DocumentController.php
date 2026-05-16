<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Revision;
use Illuminate\Http\Request;
use App\Events\DocumentUpdated;

class DocumentController extends Controller
{
    public function index()
    {
        $document = Document::first();

        if (!$document) {

            $document = Document::create([
                'title' => 'Real-Time Collaborative Document',
                'content' => ''
            ]);
        }

        return view('documents.show', compact('document'));
    }

    public function autosave(Request $request)
    {
        $document = Document::first();

        $document->update([
            'title' => $request->title,
            'content' => $request->content
        ]);

        Revision::create([
            'document_id' => $document->id,
            'content' => $request->content
        ]);

        event(new DocumentUpdated($document));

        return response()->json([
            'success' => true
        ]);
    }
}