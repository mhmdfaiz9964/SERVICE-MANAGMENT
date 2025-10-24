<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     * Apply auth middleware so only logged-in users can access.
     */
    public function __construct()
    {
        $this->middleware('auth'); // redirects to login if not logged in
    }

    /**
     * Show the admin dashboard after login.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.dashboard'); // your admin dashboard blade
    }

    /**
     * Redirect user after login.
     *
     * You can define this in LoginController or here using a redirect.
     */
    public function redirectToDashboard()
    {
        return redirect()->route('admin.dashboard'); // redirect to /admin
    }
}
