<?php

namespace App\Repositories;

use App\Models\BookingInquiry;
use App\Contracts\BookingInquiryRepositoryInterface;

class BookingInquiryRepository implements BookingInquiryRepositoryInterface
{
    public function all()
    {
        return BookingInquiry::all();
    }

    public function find($id)
    {
        return BookingInquiry::findOrFail($id);
    }

    public function create(array $data)
    {
        return BookingInquiry::create($data);
    }

    public function update($id, array $data)
    {
        $bookingInquiry = $this->find($id);
        $bookingInquiry->update($data);
        return $bookingInquiry;
    }

    public function delete($id)
    {
        $bookingInquiry = $this->find($id);
        $bookingInquiry->delete();
        return true;
    }
}