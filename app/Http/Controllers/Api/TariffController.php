<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function index()
    {
        Tariff::all();
    }
    public function show($id)
    {
        // Return a specific tariff by ID
    }
    public function store(Request $request)
    {
        // Create a new tariff
    }
    public function update(Request $request, $id)
    {
        // Update an existing tariff
    }
    public function destroy($id)
    {
        // Delete a tariff
    }
}
