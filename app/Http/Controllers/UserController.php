<?php

namespace App\Http\Controllers;

use App\Contracts\UserRepositoryInterface;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $users = $this->userRepository->all();
        return response()->json([
            'data' => $users,
            'message' => 'Users fetched successfully'
        ], 200);
    }

    public function show($id)
    {
        $user = $this->userRepository->find($id);
        return response()->json([
            'data' => $user,
            'message' => 'User fetched successfully'
        ], 200);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $user = $this->userRepository->create($data);
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $data = $request->validated();
        $user = $this->userRepository->update($id, $data);
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }

    public function destroy($id)
    {
        $this->userRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ], 200);
    }
}