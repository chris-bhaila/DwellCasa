<?php

namespace App\Http\Controllers;

use App\Models\GuestDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuestDocumentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_id'      => 'required|exists:guests,id',
            'document_type' => 'nullable|string|max:50',
            'id_number'     => 'nullable|string|max:100',
            'nationality'   => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'photo'         => 'nullable|image|max:5120',
            'notes'         => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('guest-documents', 'public');
        }

        $validated['uploaded_by'] = auth()->id();

        $document = GuestDocument::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'ID document saved successfully.',
            'data'    => $document,
        ], 201);
    }

    public function show(int $guestId)
    {
        $document = GuestDocument::where('guest_id', $guestId)->latest()->first();

        if (!$document) {
            return response()->json(['data' => null], 200);
        }

        return response()->json([
            'data' => array_merge($document->toArray(), [
                'photo_url' => $document->photo ? asset('storage/' . $document->photo) : null,
            ]),
        ], 200);
    }

    public function destroy(GuestDocument $guestDocument)
    {
        if ($guestDocument->photo) {
            Storage::disk('public')->delete($guestDocument->photo);
        }

        $guestDocument->delete();

        return response()->json(['success' => true, 'message' => 'Document deleted.']);
    }
}
