<?php

namespace App\Repositories;

use App\Contracts\FaqRepositoryInterface;
use App\Models\Faq;
use Illuminate\Support\Collection;

class FaqRepository implements FaqRepositoryInterface
{
    public function all(): Collection
    {
        return Faq::orderBy('sort_order')->orderBy('created_at')->get();
    }

    public function find(int $id): Faq
    {
        return Faq::findOrFail($id);
    }

    public function create(array $data): Faq
    {
        return Faq::create($data);
    }

    public function update(Faq $faq, array $data): Faq
    {
        $faq->fill($data);
        $faq->save();
        return $faq;
    }

    public function delete(Faq $faq): void
    {
        $faq->delete();
    }
}
