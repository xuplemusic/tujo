<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Audit::class); // We will need an AuditPolicy for this

        $audits = Audit::with('user')->latest()->paginate(20);

        return Inertia::render('Audit/Index', [
            'audits' => $audits,
        ]);
    }
}
