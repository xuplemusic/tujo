<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Citizen;
use Illuminate\Http\Request;

class CitizenController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Citizen  $citizen
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Citizen $citizen)
    {
        // In a real-world scenario, you might want to use API Resources
        // to control which fields are exposed.
        return response()->json($citizen);
    }
}
