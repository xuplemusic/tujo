<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Citizen;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function __invoke(Request $request)
    {
        // In a real application, you would fetch more complex data.
        // For now, we'll just fetch some basic counts.
        $stats = [
            'total_citizens' => Citizen::count(),
            'total_users' => User::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_registrars' => User::where('role', 'registrar')->count(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}
