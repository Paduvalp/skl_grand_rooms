<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::orderBy('sort_order')->orderByDesc('id')->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function create()
    {
        return view('admin.reviews.create', ['review' => new Review(['rating' => 5, 'source' => 'google', 'is_active' => true, 'sort_order' => 0])]);
    }

    public function store(Request $request)
    {
        Review::create($this->validated($request));

        return redirect()->route('admin.reviews.index')->with('success', 'Review added.');
    }

    public function edit(Review $review)
    {
        return view('admin.reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        $review->update($this->validated($request));

        return redirect()->route('admin.reviews.index')->with('success', 'Review updated.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'guest_name' => ['required', 'string', 'max:120'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review_text' => ['required', 'string', 'max:2000'],
            'source' => ['required', Rule::in(array_keys(Review::SOURCES))],
            'stay_date' => ['nullable', 'date'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
        ], [], ['guest_name' => 'guest name', 'review_text' => 'review']);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
