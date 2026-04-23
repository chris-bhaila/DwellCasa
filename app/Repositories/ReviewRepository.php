<?php

namespace App\Repositories;

use App\Models\Review;
use App\Contracts\ReviewRepositoryInterface;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function all()
    {
        return Review::with(['roomType', 'guest'])->latest()->get();
    }

    public function find($id)
    {
        return Review::with(['roomType', 'guest'])->findOrFail($id);
    }

    public function findByToken(string $token)
    {
        return Review::where('review_token', $token)
            ->where('token_used', false)
            ->firstOrFail();
    }

    public function create(array $data)
    {
        return Review::create($data);
    }

    public function update($id, array $data)
    {
        $review = $this->find($id);
        $review->update($data);
        return $review;
    }

    public function delete($id)
    {
        $review = $this->find($id);
        $review->delete();
        return true;
    }
}