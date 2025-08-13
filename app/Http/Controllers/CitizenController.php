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

    /**
     * Store the face image and call the Clarifai API for verification.
     */
    public function storeFace(Request $request, Citizen $citizen)
    {
        $request->validate(['image' => 'required|image']);

        // IMPORTANT: This implementation is a "best guess" based on the Clarifai
        // Quick Start guide, as the detailed HTTP API documentation was not accessible.
        // The user must verify the endpoint URL, model ID, and request payload format.

        $pat = config('clarifai.pat');
        $userId = config('clarifai.user_id');
        $appId = config('clarifai.app_id');
        $modelId = config('clarifai.model_id');

        if (!$pat || !$userId || !$appId) {
            return back()->withErrors(['api_error' => 'Clarifai API is not configured. Please check your .env file.']);
        }

        // 1. Store the image locally
        $path = $request->file('image')->store('citizens/faces', 'public');
        $citizen->update(['face_image_path' => $path]);

        // 2. Prepare the data for the Clarifai API
        $imageData = base64_encode(file_get_contents($request->file('image')->getRealPath()));
        $apiUrl = "https://api.clarifai.com/v2/users/{$userId}/apps/{$appId}/models/{$modelId}/outputs";

        // 3. Call the Clarifai API
        $response = Http::withHeaders([
            'Authorization' => 'Key ' . $pat,
            'Content-Type' => 'application/json',
        ])->post($apiUrl, [
            'inputs' => [
                [
                    'data' => [
                        'image' => [
                            'base64' => $imageData
                        ]
                    ]
                ]
            ]
        ]);

        if ($response->failed()) {
            return back()->withErrors(['api_error' => 'Clarifai API request failed: ' . $response->reason()]);
        }

        // 4. TODO for user: Process the response.
        // The response will contain data about the detected face(s), including bounding boxes
        // and potentially embeddings. You would typically store this data or use it for
        // duplicate checking by calling a "search" endpoint.
        // For now, we just confirm the API call was successful.

        return back()->with('success', 'Face image uploaded and successfully processed by Clarifai.');
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
