<?php

namespace App\Http\Controllers\Api\Add;

use App\Http\Controllers\Controller;
use App\Http\Requests\addressesRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Services\AddressService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    private $addr;
    public function __construct(AddressService $addr)
    {
        $this->addr = $addr;
    }

    private function user()
    {
        return request()->user();
    }

    public function index(Request $request)
    {
        $user = $this->user();
        $data = $this->addr->getAddresses($user);

        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'Addresses fetched successfully',
        ]);
    }

    public function store(addressesRequest $request)
    {
        $user = $this->user();

        $address = $this->addr->store($user, $request->validated());

        return response()->json([
            'status' => true,
            'data' => $address,
            'message' => 'Address created successfully',
        ]);
    }

    public function set_default($id)
    {

        $user = $this->user();
        $address = $this->addr->setDefault($user, $id);

        return response()->json([
            'status' => true,
            'data' => $address,
            'message' => 'Address set as default successfully',
        ]);
    }
    public function show($id)
    {
        $user = $this->user();
        $address = $this->addr->show_address($user, $id);
        return response()->json(data: [
            'status' => true,
            'data' => $address,
            'message' => 'your address info',
        ]);
    }
    public function update(UpdateAddressRequest $request, $id)
    {
        $user = $this->user();
        $address = $this->addr->updateAddress($user, $request->validated(), $id);
        return response()->json(data: [
            'status' => true,
            'data' => $address,
            'message' => 'your address updated successfully',
        ]);
    }

    public function delete($id)
    {
        $user = $this->user();
        $address = $this->addr->delete_address($user, $id);
        return response()->json([
            "status" => $address,
            "message" => "Address deleted successfully"
        ]);
    }
}
