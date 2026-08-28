<?php

namespace App\Http\Controllers\Master;


use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::latest()->get();

        return view('areas.index', compact('areas'));
    }
}