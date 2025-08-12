<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CitizenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Citizen::class);
        return Inertia::render('Citizen/Index', [
            'citizens' => Citizen::all(),
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

        // Store the image locally first
        $path = $request->file('image')->store('citizens/faces', 'public');
        $citizen->update(['face_image_path' => $path]);

        // Now, call the external biometric API
        $apiUrl = config('biometrics.api_url');
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

        // Assuming the API returns a JSON response with a unique identifier or embedding
        // $biometricId = $response->json('biometric_id');
        // $citizen->update(['face_embedding' => $biometricId]);

        return back()->with('success', 'Face image uploaded and sent to biometric service.');
    }

    /**
     * Store the fingerprint template and call the biometric API.
     */
    public function storeFingerprint(Request $request, Citizen $citizen)
    {
        $request->validate(['fingerprint_data' => 'required|string']);

        $fingerprintTemplate = $request->input('fingerprint_data');
        $citizen->update(['fingerprint_template' => $fingerprintTemplate]);

        // Now, call the external biometric API
        $apiUrl = config('biometrics.api_url');
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

        // $biometricId = $response->json('biometric_id');
        // $citizen->update(['fingerprint_embedding' => $biometricId]);

        return back()->with('success', 'Fingerprint template stored and sent to biometric service.');
    }
}
