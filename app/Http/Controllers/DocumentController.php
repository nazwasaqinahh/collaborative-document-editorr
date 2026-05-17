<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Revision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $revisions = Revision::where(
            'document_id',
            $document->id
        )
        ->latest()
        ->take(10)
        ->get();

        return view(
            'documents.show',
            compact(
                'document',
                'revisions'
            )
        );
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
            'content' => $request->content,
            'user_id' => Auth::id()
        ]);

        broadcast(
            new DocumentUpdated($document)
        )->toOthers();

        return response()->json([
            'success' => true
        ]);
    }

   public function restore($id)
{
    $revision = Revision::findOrFail($id);

    $document = Document::find(
        $revision->document_id
    );

    $document->update([

        'content' => $revision->content

    ]);

    broadcast(
        new DocumentUpdated($document)
    )->toOthers();

    return redirect('/');
}
}