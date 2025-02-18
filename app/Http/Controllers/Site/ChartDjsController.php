<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Deejay;
use Illuminate\Http\Request;

class ChartDjsController extends Controller
{
    public function index()
    {
        $djs = Deejay::where('is_active', true)->with('recommendations')->get();
        return view('site.djcharts.index', compact('djs'));
    }

    public function show(Deejay $dj)
    {
        $recommendations = $dj->recommendations()->with('artists')->orderBy('order')->get();
        return view('site.djcharts.show', compact('dj', 'recommendations'));
    }
}
