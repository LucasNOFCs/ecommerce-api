<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(
        Request $request,
        AddressService $addressService
    ): JsonResponse {
        $addresses = $addressService->getAddresses(
            $request->user()
        );

        return response()->json([
            'data' => $addresses,
        ]);
    }

    public function store(
        StoreAddressRequest $request,
        AddressService $addressService
    ) {
        $address = $addressService->createAddress(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Address created successfully.',
            'data' => $address,
        ], 201);
    }

    public function show(
        Request $request,
        int $address,
        AddressService $addressService
    ): JsonResponse {
        $result = $addressService->getAddress(
            $request->user(),
            $address
        );

        return response()->json([
            'data' => $result,
        ]);
    }

    public function update(
        UpdateAddressRequest $request,
        int $address,
        AddressService $addressService
    ): JsonResponse {
        $result = $addressService->updateAddress(
            $request->user(),
            $address,
            $request->validated()
        );

        return response()->json([
            'message' => 'Address updated successfully.',
            'data' => $result,
        ]);
    }

    public function destroy(
        Request $request,
        int $address,
        AddressService $addressService
    ): JsonResponse {
        $addressService->deleteAddress(
            $request->user(),
            $address
        );

        return response()->json([
            'message' => 'Address deleted successfully.',
        ]);
    }

    public function setDefault(
        Request $request,
        int $address,
        AddressService $addressService
    ): JsonResponse {
        $result = $addressService->setDefaultAddress(
            $request->user(),
            $address
        );

        return response()->json([
            'message' => 'Default address updated successfully.',
            'data' => $result,
        ]);
    }
}
