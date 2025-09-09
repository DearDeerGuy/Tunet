<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateReviewsRequest;
use App\Http\Requests\ReviewsRequest;
use App\Models\Films;
use App\Models\Reviews;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReviewsController extends Controller
{
    public function index(ReviewsRequest $request)
    {
        $data = $request->validated();

        $film = Films::find($data['film_id'])->reviews();
        return response()->json(
            $film->paginate($data['perPage'])
        );
    }

    public function store(CreateReviewsRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id']=Auth::id();

        $reviews = Reviews::create($validated);
        return response()->json($reviews, 201);
}
    public function show(Reviews $reviews)
    {
        return response()->json($reviews);
    }
    public function update(CreateReviewsRequest $request, Reviews $reviews): JsonResponse
    {
        $validated = $request->validated();

        $reviews->update($validated);
        return response()->json($reviews);
    }
    public function destroy(Reviews $reviews): JsonResponse
    {
        $reviews->delete();
        return response()->json(null, 204);
    }

}
