<?php

namespace App\Http\Controllers;

use App\Contracts\FaqRepositoryInterface;
use App\Http\Requests\StoreFaqRequest;
use App\Http\Requests\UpdateFaqRequest;

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
