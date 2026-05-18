<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Revision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\DocumentUpdated;

class DocumentController extends Controller
{
    public function documents()
    {
        $documents = Document::latest()->get();

        return view(
            'documents.index',
            compact('documents')
        );
    }

    public function show($id)
    {
        $document = Document::findOrFail($id);

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

    public function create()
    {
        return view(
            'documents.create'
        );
    }

    public function store(Request $request)
    {
        $document = Document::create([

            'title' => $request->title,

            'content' => ''

        ]);

        return redirect(
            '/documents/' .
            $document->id
        );
    }

    public function autosave(
        Request $request,
        $id
    )
    {
        $document = Document::findOrFail($id);

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

            new DocumentUpdated(

                $document,

                Auth::user()->name

            )

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

            new DocumentUpdated(

                $document,

                Auth::user()->name

            )

        )->toOthers();

        return redirect(
            '/documents/' .
            $document->id
        );
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);

        $document->delete();

        return redirect('/documents');
    }
}