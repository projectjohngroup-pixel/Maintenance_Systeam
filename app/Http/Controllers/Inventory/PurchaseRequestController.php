<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest\PurchaseRequest;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $purchaseRequests = PurchaseRequest::latest()->paginate(20);

        return view(
            'inventory.purchase-request.index',
            compact('purchaseRequests')
        );
    }

    public function create()
    {
        return view('inventory.purchase-request.create');
    }

    public function store()
    {
        //
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        //
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        //
    }

    public function update()
    {
        //
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        //
    }
}