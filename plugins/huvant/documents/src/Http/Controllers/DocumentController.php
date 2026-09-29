<?php

namespace Huvant\Documents\Http\Controllers;

use Huvant\Documents\Models\Document;
use Huvant\Documents\Support\Documents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DocumentController
{
    /** Files are private: served only to who can see the project or task. */
    public function show(Request $request, Document $document): Response
    {
        abort_unless(auth()->check(), 403);
        abort_unless($document->isFile() && Documents::canView($document), 404);
        $disk = Storage::disk($document->disk ?: Documents::DISK);
        abort_unless($disk->exists($document->path), 404);

        $inline = $request->boolean('inline') && $document->previewable();
        $name = $document->original_name ?: $document->title;
        $headers = [
            'Content-Type'           => $document->mime_type ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
            // Even inline, an uploaded file never runs as a page of the app.
            'Content-Security-Policy' => "default-src 'none'; img-src 'self'; style-src 'unsafe-inline'; sandbox",
        ];

        return $inline
            ? $disk->response($document->path, $name, $headers, 'inline')
            : $disk->download($document->path, $name, $headers);
    }
}
