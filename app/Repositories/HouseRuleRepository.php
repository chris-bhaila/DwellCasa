<?php

namespace App\Repositories;

use App\Models\HouseRule;
use App\Contracts\HouseRuleRepositoryInterface;

class HouseRuleRepository implements HouseRuleRepositoryInterface
{
    public function all()
    {
        return HouseRule::all();
    }

    public function find($id)
    {
        return HouseRule::findOrFail($id);
    }

    public function create(array $data)
    {
        return HouseRule::create($data);
    }

    public function update($id, array $data)
    {
        $houseRule = $this->find($id);
        $houseRule->update($data);
        return $houseRule;
    }

    public function delete($id)
    {
        $houseRule = $this->find($id);
        $houseRule->delete();
        return true;
    }
}