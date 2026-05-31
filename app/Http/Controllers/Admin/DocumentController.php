<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\LegalCase;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with(['case', 'client', 'uploader']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('doc_number', 'like', "%$search%");
            });
        }

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        $documents = $query->latest()->paginate(20);

        return view('admin.documents.index', compact('documents'));
    }

    public function create()
    {
        $cases   = LegalCase::with('client')->orderBy('case_number')->get();
        $clients = Client::orderBy('first_name')->get();

        return view('admin.documents.create', compact('cases', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:200',
            'category'        => 'required|string',
            'case_id'         => 'nullable|exists:legal_cases,id',
            'client_id'       => 'nullable|exists:clients,id',
            'description'     => 'nullable|string',
            'is_confidential' => 'boolean',
            'file'            => 'required|file|max:20480',
        ]);

        $file      = $request->file('file');
        $path      = $file->store('documents', 'public');
        $docNumber = Document::generateNumber();

        Document::create([
            'doc_number'      => $docNumber,
            'title'           => $request->title,
            'category'        => $request->category,
            'case_id'         => $request->case_id,
            'client_id'       => $request->client_id,
            'description'     => $request->description,
            'is_confidential' => $request->boolean('is_confidential'),
            'file_path'       => $path,
            'file_name'       => $file->getClientOriginalName(),
            'file_size'       => $file->getSize(),
            'mime_type'       => $file->getMimeType(),
            'uploaded_by'     => Auth::id(),
        ]);

        return redirect()->route('admin.documents.index')
            ->with('success', "Document {$docNumber} uploaded successfully.");
    }

    public function show(Document $document)
    {
        $document->load(['case', 'client', 'uploader']);

        return view('admin.documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $cases   = LegalCase::with('client')->orderBy('case_number')->get();
        $clients = Client::orderBy('first_name')->get();

        return view('admin.documents.edit', compact('document', 'cases', 'clients'));
    }

    public function update(Request $request, Document $document)
    {
        $request->validate([
            'title'           => 'required|string|max:200',
            'category'        => 'required|string',
            'case_id'         => 'nullable|exists:legal_cases,id',
            'client_id'       => 'nullable|exists:clients,id',
            'description'     => 'nullable|string',
            'is_confidential' => 'boolean',
            'file'            => 'nullable|file|max:20480',
        ]);

        $data = [
            'title'           => $request->title,
            'category'        => $request->category,
            'case_id'         => $request->case_id,
            'client_id'       => $request->client_id,
            'description'     => $request->description,
            'is_confidential' => $request->boolean('is_confidential'),
        ];

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($document->file_path);
            $file             = $request->file('file');
            $data['file_path'] = $file->store('documents', 'public');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['mime_type'] = $file->getMimeType();
        }

        $document->update($data);

        return redirect()->route('admin.documents.show', $document)
            ->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $number = $document->doc_number;
        $document->delete();

        return redirect()->route('admin.documents.index')
            ->with('success', "Document {$number} deleted.");
    }

    public function download(Document $document): StreamedResponse
    {
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}
