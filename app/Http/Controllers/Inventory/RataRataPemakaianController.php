<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RataRataPemakaianController extends Controller
{
    public function index(Request $request)
    {
        return view('inventory.rata-rata-pemakaian.index');
    }
}