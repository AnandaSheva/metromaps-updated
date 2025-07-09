<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function setSession(Request $request)
    {
        // Set session to indicate user has visited welcome page
        session(['visited_welcome' => true]);
        
        return response()->json(['status' => 'success']);
    }
}
