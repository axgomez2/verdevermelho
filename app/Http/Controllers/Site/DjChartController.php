<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\DJ;
use Illuminate\Http\Request;
use App\Models\VinylMaster;

class DjChartController extends Controller
{
    public function show(DJ $dj)
    {
        $recommendations = $dj->recommendations()->orderBy('order')->get();
        return view('site.recommendations.show', compact('dj', 'recommendations'));
    }
}
