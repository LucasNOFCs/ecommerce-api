<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {

        $result = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'User registered successfully',
            'data' => $result,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'message' => 'Login successfully',
            'data' => $result,
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logout successful',
            'data' => null,
        ], 200);
    }

    public function me(Request $request): JsonResponse
    {
        $me = $this->authService->me($request);

        return response()->json([
            'message' => 'Returned authenticated user.',
            'data' => [
                'id' => $me->id,
                'name' => $me->name,
                'email' => $me->email,
                'role' => $me->role,
                'created_at' => $me->created_at,
            ],
        ]);
    }

    public function updateMe(UpdateUserRequest $request): JsonResponse
    {
        $me = $this->authService->updateMe($request->user(), $request->validated());

        return response()->json([
            'message' => 'User has been updated.',
            'data' => [
                'id' => $me->id,
                'name' => $me->name,
                'email' => $me->email,
            ],
        ]);
    }

    public function deleteMe()
    {
        $status = $this->authService->deleteMe();

        return response()->json([
            'message' => 'User has been deleted.',
            'data' => null,
        ]);
    }
}
