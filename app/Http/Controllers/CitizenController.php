<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Intervention\Image\Facades\Image;

class CitizenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Citizen::class);

        $query = Citizen::query();

        if (FacadesRequest::input('search')) {
            $search = FacadesRequest::input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('lga', 'like', "%{$search}%");
        }

        return Inertia::render('Citizen/Index', [
            'citizens' => $query->paginate(10)->withQueryString(),
            'filters' => FacadesRequest::only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Citizen::class);
        return Inertia::render('Citizen/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Citizen::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'lga' => 'required|string|max:255',
        ]);

        $citizen = Citizen::create($request->all());

        return redirect()->route('citizens.index')->with('success', 'Citizen created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Citizen $citizen)
    {
        $this->authorize('view', $citizen);
        return Inertia::render('Citizen/Show', [
            'citizen' => $citizen
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Citizen $citizen)
    {
        $this->authorize('update', $citizen);
        return Inertia::render('Citizen/Edit', [
            'citizen' => $citizen
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Citizen $citizen)
    {
        $this->authorize('update', $citizen);
        $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'lga' => 'required|string|max:255',
        ]);

        $citizen->update($request->all());

        return redirect()->route('citizens.index')->with('success', 'Citizen updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Citizen $citizen)
    {
        $this->authorize('delete', $citizen);
        $citizen->delete();
        return redirect()->route('citizens.index')->with('success', 'Citizen deleted successfully.');
    }

use Illuminate\Support\Facades\Http;

    /**
     * Store the face image and call the biometric API.
     */
    public function storeFace(Request $request, Citizen $citizen)
    {
        $request->validate(['image' => 'required|image']);

        $apiUrl = config('biometrics.api_url');
        if ($apiUrl) {
            // Step 1: Check for duplicates before doing anything else
            $imageFile = $request->file('image');
            $duplicateCheckResponse = Http::attach(
                'face_image', $imageFile->get(), $imageFile->getClientOriginalName()
            )->post("{$apiUrl}/check-duplicate-face");

            if ($duplicateCheckResponse->failed()) {
                return back()->withErrors(['api_error' => 'The duplicate check service failed.']);
            }

            if ($duplicateCheckResponse->json('is_duplicate')) {
                $matchedId = $duplicateCheckResponse->json('matched_citizen_id');
                return back()->withErrors(['duplicate' => "A citizen with similar biometric data already exists (ID: {$matchedId})."]);
            }
        }

        // Store the image locally first
        $path = $request->file('image')->store('citizens/faces', 'public');
        $citizen->update(['face_image_path' => $path]);

        // Now, call the external biometric API for verification/storage
        if (!$apiUrl) {
            return back()->with('info', 'Face image saved, but biometric API is not configured.');
        }

        $response = Http::attach(
            'face_image', file_get_contents(storage_path("app/public/{$path}")), basename($path)
        )->post("{$apiUrl}/verify-face", [
            'citizen_id' => $citizen->id,
        ]);

        if ($response->failed()) {
            return back()->withErrors(['api_error' => 'The biometric service failed to process the image.']);
        }

        return back()->with('success', 'Face image uploaded and sent to biometric service.');
    }

    /**
     * Store the fingerprint template and call the biometric API.
     */
    public function storeFingerprint(Request $request, Citizen $citizen)
    {
        $request->validate(['fingerprint_data' => 'required|string']);

        $fingerprintTemplate = $request->input('fingerprint_data');

        $apiUrl = config('biometrics.api_url');
        if ($apiUrl) {
            // Step 1: Check for duplicates
            $duplicateCheckResponse = Http::post("{$apiUrl}/check-duplicate-fingerprint", [
                'fingerprint_template' => $fingerprintTemplate,
            ]);

            if ($duplicateCheckResponse->failed()) {
                return back()->withErrors(['api_error' => 'The duplicate check service failed.']);
            }

            if ($duplicateCheckResponse->json('is_duplicate')) {
                $matchedId = $duplicateCheckResponse->json('matched_citizen_id');
                return back()->withErrors(['duplicate' => "A citizen with similar biometric data already exists (ID: {$matchedId})."]);
            }
        }

        $citizen->update(['fingerprint_template' => $fingerprintTemplate]);

        // Now, call the external biometric API for verification/storage
        if (!$apiUrl) {
            return back()->with('info', 'Fingerprint template saved, but biometric API is not configured.');
        }

        $response = Http::post("{$apiUrl}/verify-fingerprint", [
            'citizen_id' => $citizen->id,
            'fingerprint_template' => $fingerprintTemplate,
        ]);

        if ($response->failed()) {
            return back()->withErrors(['api_error' => 'The biometric service failed to process the fingerprint.']);
        }

        return back()->with('success', 'Fingerprint template stored and sent to biometric service.');
    }

    /**
     * Export all citizens as a CSV file.
     */
    public function exportCsv()
    {
        $this->authorize('viewAny', Citizen::class); // Or a more specific export policy

        $fileName = 'citizens.csv';
        $citizens = Citizen::all();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('ID', 'Name', 'Date of Birth', 'LGA', 'Created At');

        $callback = function() use($citizens, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($citizens as $citizen) {
                fputcsv($file, array(
                    $citizen->id,
                    $citizen->name,
                    $citizen->date_of_birth,
                    $citizen->lga,
                    $citizen->created_at
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
