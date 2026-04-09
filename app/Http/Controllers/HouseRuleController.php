<?php

namespace App\Http\Controllers;

use App\Contracts\HouseRuleRepositoryInterface;
use App\Http\Requests\StoreHouseRuleRequest;
use App\Http\Requests\UpdateHouseRuleRequest;

class HouseRuleController extends Controller
{
    protected $houseRuleRepository;

    public function __construct(HouseRuleRepositoryInterface $houseRuleRepository)
    {
        $this->houseRuleRepository = $houseRuleRepository;
    }

    public function index()
    {
        $houseRules = $this->houseRuleRepository->all();
        return response()->json(['data' => $houseRules, 'message' => 'House rules fetched successfully'], 200);
    }

    public function show($id)
    {
        $houseRule = $this->houseRuleRepository->find($id);
        return response()->json(['data' => $houseRule, 'message' => 'House rule fetched successfully'], 200);
    }

    public function store(StoreHouseRuleRequest $request)
    {
        $houseRule = $this->houseRuleRepository->create($request->validated());
        return response()->json(['success' => true, 'message' => 'House rule created successfully', 'data' => $houseRule], 201);
    }

    public function update(UpdateHouseRuleRequest $request, $id)
    {
        $houseRule = $this->houseRuleRepository->update($id, $request->validated());
        return response()->json(['success' => true, 'message' => 'House rule updated successfully', 'data' => $houseRule], 200);
    }

    public function destroy($id)
    {
        $this->houseRuleRepository->delete($id);
        return response()->json(['success' => true, 'message' => 'House rule deleted successfully'], 200);
    }
}
