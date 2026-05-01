<?php

namespace App\Http\Controllers;

use App\Contracts\InquiryRepositoryInterface;
use App\Http\Requests\StoreInquiryRequest;
use App\Http\Requests\UpdateInquiryRequest;
use App\Mail\InquiryReplyMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    protected $inquiryRepository;

    public function __construct(InquiryRepositoryInterface $inquiryRepository)
    {
        $this->inquiryRepository = $inquiryRepository;
    }

    public function index(Request $request)
    {
        $inquiries = $this->inquiryRepository->all();
        return response()->json([
            'data' => $inquiries,
            'message' => 'Inquiries fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $inquiry = $this->inquiryRepository->find($id);
        return response()->json([
            'data' => $inquiry,
            'message' => 'Inquiry fetched successfully'
        ], 200);
    }

    public function store(StoreInquiryRequest $request)
    {
        $user = auth()->user();
        $locationId = $user->hasRole('super_admin')
            ? session('selected_location_id')
            : $user->location_id;

        abort_if(!$locationId, 422, 'No location selected.');

        $data = $request->validated();
        $data['location_id'] = $locationId;

        $inquiry = $this->inquiryRepository->create($data);
        return response()->json([
            'success' => true,
            'message' => 'Inquiry submitted successfully',
            'data'    => $inquiry
        ], 201);
    }

    public function update(UpdateInquiryRequest $request, $id)
    {
        $data = $request->validated();
        unset($data['location_id']);

        $inquiry = $this->inquiryRepository->update($id, $data);
        return response()->json([
            'success' => true,
            'message' => 'Inquiry updated successfully',
            'data'    => $inquiry
        ], 200);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $inquiry = $this->inquiryRepository->find($id);

        try {
            Mail::to($inquiry->email)->send(new InquiryReplyMail($inquiry, $request->subject, $request->message));

            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Inquiry reply email failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reply'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $this->inquiryRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Inquiry deleted successfully'
        ], 200);
    }
}
