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
            'document_type' => 'required|string|max:50',
            'id_number'     => 'required|string|max:100',
            'nationality'   => 'required|string|max:100',
            'date_of_birth' => 'required|date',
            'photo'         => 'nullable|image|max:5120',
            'notes'         => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('guest-documents', 'local');
        }

        $validated['uploaded_by'] = auth()->id();

        $document = GuestDocument::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'ID document saved successfully.',
            'data'    => $document,
        ], 201);
    }

    public function photo(GuestDocument $guestDocument)
    {
        if (!$guestDocument->photo || !Storage::disk('local')->exists($guestDocument->photo)) {
            abort(404);
        }

        return Storage::disk('local')->response($guestDocument->photo);
    }

    public function show(int $guestId)
    {
        $document = GuestDocument::where('guest_id', $guestId)->latest()->first();

        if (!$document) {
            return response()->json(['data' => null], 200);
        }

        return response()->json([
            'data' => array_merge($document->toArray(), [
                'photo_url' => $document->photo
                    ? route('admin.guest-documents.photo', $document->id)
                    : null,
            ]),
        ], 200);
    }

    public function update(Request $request, GuestDocument $guestDocument)
    {
        $validated = $request->validate([
            'document_type' => 'required|string|max:50',
            'id_number'     => 'required|string|max:100',
            'nationality'   => 'required|string|max:100',
            'date_of_birth' => 'required|date',
            'photo'         => 'nullable|image|max:5120',
            'notes'         => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            if ($guestDocument->photo) {
                Storage::disk('local')->delete($guestDocument->photo);
            }
            $validated['photo'] = $request->file('photo')->store('guest-documents', 'local');
        } else {
            unset($validated['photo']);
        }

        $guestDocument->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'ID document updated successfully.',
            'data'    => $guestDocument->fresh(),
        ]);
    }

    public function destroy(GuestDocument $guestDocument)
    {
        if ($guestDocument->photo) {
            Storage::disk('local')->delete($guestDocument->photo);
        }

        $guestDocument->delete();

        return response()->json(['success' => true, 'message' => 'Document deleted.']);
    }
}
