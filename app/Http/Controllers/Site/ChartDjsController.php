<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\DJ;
use Illuminate\Http\Request;

class ChartDjsController extends Controller
{
    public function index()
    {
        $djs = DJ::where('is_active', true)->with('recommendations')->get();
        return view('site.djcharts.index', compact('djs'));
    }

    public function show(DJ $dj)
    {
        $recommendations = $dj->recommendations()->with('artists')->orderBy('order')->get();
        return view('site.djcharts.show', compact('dj', 'recommendations'));
    }
}
