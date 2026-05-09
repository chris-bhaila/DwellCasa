<?php

namespace App\Contracts;

use App\Models\Faq;
use Illuminate\Support\Collection;

interface FaqRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): Faq;
    public function create(array $data): Faq;
    public function update(Faq $faq, array $data): Faq;
    public function delete(Faq $faq): void;
}
