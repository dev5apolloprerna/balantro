<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark'
        ]);
        
        // Store theme preference in session
        session(['theme' => $request->theme]);
        
        return response()->json(['success' => true, 'theme' => $request->theme]);
    }
}
