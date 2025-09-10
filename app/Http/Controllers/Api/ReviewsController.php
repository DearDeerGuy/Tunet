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

        $reviews = Reviews::where('film_id', $data['film_id']);
        if (isset($data["user_id"]) && $data['user_id']) {
            $reviews->where('user_id', $data['user_id']);
        }
        return response()->json(
            $reviews->paginate($data['perPage'])
        );
    }
    public function show(Reviews $review)
    {
        return response()->json($review);
    }
    public function store(CreateReviewsRequest $request)
    {
        $validated = $request->validated();

        $review = Reviews::create($validated);
        return response()->json($review, 201);
    }
    public function update(CreateReviewsRequest $request, Reviews $review): JsonResponse
    {
        $validated = $request->validated();
        $review->update($validated);

        return response()->json($review);
    }
    public function destroy(Reviews $review): JsonResponse
    {
        $review->delete();
        return response()->json(null, 204);
    }

}
