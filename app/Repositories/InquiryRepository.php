<?php

namespace App\Repositories;

use App\Models\Inquiry;
use App\Contracts\InquiryRepositoryInterface;

class InquiryRepository implements InquiryRepositoryInterface
{
    public function all()
    {
        return Inquiry::latest()->get();
    }

    public function find($id)
    {
        return Inquiry::findOrFail($id);
    }

    public function create(array $data)
    {
        return Inquiry::create($data);
    }

    public function update($id, array $data)
    {
        $inquiry = $this->find($id);
        $inquiry->update($data);
        return $inquiry;
    }

    public function delete($id)
    {
        $inquiry = $this->find($id);
        $inquiry->delete();
        return true;
    }
}