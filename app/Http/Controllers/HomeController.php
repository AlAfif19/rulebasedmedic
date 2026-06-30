<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\Medicine;
use App\Models\Symptom;
use App\Models\Rule;

class HomeController extends Controller
{
    public function landing()
    {
        return view('landing', [
            'symptomCount' => Symptom::count(),
            'diseaseCount' => Disease::count(),
            'medicineCount' => Medicine::count(),
            'ruleCount' => Rule::count(),
        ]);
    }

    public function dashboard()
    {
        return view('user.dashboard', [
            'featuredDiseases' => Disease::query()->where('is_active', true)->take(6)->get(),
            'medicineCount' => Medicine::count(),
            'symptomCount' => Symptom::count(),
        ]);
    }

    public function information()
    {
        return view('information', [
            'medicines' => Medicine::query()->where('is_active', true)->orderBy('name')->paginate(12),
        ]);
    }
}
