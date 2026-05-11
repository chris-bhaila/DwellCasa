<?php

namespace App\Http\Controllers;

use App\Contracts\FaqRepositoryInterface;
use App\Http\Requests\StoreFaqRequest;
use App\Http\Requests\UpdateFaqRequest;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function __construct(protected FaqRepositoryInterface $faqRepository) {}

    public function index()
    {
        $faqs = $this->faqRepository->all();

        return response()->json([
            'data'    => $faqs,
            'message' => 'FAQs fetched successfully',
        ]);
    }

    public function store(StoreFaqRequest $request)
    {
        $user       = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;

        abort_if(!$locationId, 422, 'No location selected.');

        $data                = $request->validated();
        $data['location_id'] = $locationId;

        $faq = $this->faqRepository->create($data);

        activity()
            ->causedBy($user)
            ->performedOn($faq)
            ->withProperties(['location_id' => $locationId])
            ->log('FAQ created');

        return response()->json([
            'success' => true,
            'message' => 'FAQ created successfully',
            'data'    => $faq,
        ], 201);
    }

    public function update(UpdateFaqRequest $request, int $id)
    {
        $faq  = $this->faqRepository->find($id);
        $data = $request->validated();

        $faq = $this->faqRepository->update($faq, $data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($faq)
            ->withProperties(['location_id' => $faq->location_id])
            ->log('FAQ updated');

        return response()->json([
            'success' => true,
            'message' => 'FAQ updated successfully',
            'data'    => $faq,
        ]);
    }

    public function importFrom(Request $request)
    {
        abort_unless(auth()->user()->hasRole('super_admin'), 403);

        $request->validate(['source_location_id' => 'required|integer|exists:locations,id']);

        $targetLocationId = session('selected_location_id');
        abort_if(!$targetLocationId, 422, 'No location selected.');
        abort_if($request->source_location_id == $targetLocationId, 422, 'Source and target location are the same.');

        $source = Faq::withoutGlobalScopes()
            ->where('location_id', $request->source_location_id)
            ->get();

        Faq::withoutGlobalScopes()
            ->where('location_id', $targetLocationId)
            ->delete();

        foreach ($source as $faq) {
            Faq::withoutGlobalScopes()->create([
                'question'    => $faq->question,
                'answer'      => $faq->answer,
                'sort_order'  => $faq->sort_order,
                'is_active'   => $faq->is_active,
                'location_id' => $targetLocationId,
            ]);
        }

        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => $targetLocationId, 'source_location_id' => $request->source_location_id])
            ->log('FAQs replaced by import');

        return response()->json([
            'success' => true,
            'message' => "Imported {$source->count()} FAQ(s) successfully. Previous FAQs have been replaced.",
        ]);
    }

    public function destroy(int $id)
    {
        $faq = $this->faqRepository->find($id);

        activity()
            ->causedBy(auth()->user())
            ->withProperties(['location_id' => $faq->location_id])
            ->log('FAQ deleted');

        $this->faqRepository->delete($faq);

        return response()->json([
            'success' => true,
            'message' => 'FAQ deleted successfully',
        ]);
    }
}
