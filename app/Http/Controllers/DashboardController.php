<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lokasi;

class DashboardController extends Controller
{
    public function index()
    {

        $lokasis = Lokasi::all();
        return view('dashboard.index', compact('lokasis'));
    }
}
