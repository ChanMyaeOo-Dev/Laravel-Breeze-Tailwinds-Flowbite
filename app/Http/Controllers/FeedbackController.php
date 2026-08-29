<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Http\Requests\UpdateFeedbackRequest;
use App\Models\Feedback;
use App\Traits\RestaurantScoped;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    use RestaurantScoped;

    public function index()
    {
        $feedbacks = Feedback::with('analysis')
            ->where('restaurant_id', Auth::id())
            ->latest()
            ->get();

        $stats = [
            'total' => $feedbacks->count(),
            'analyzed' => $feedbacks->filter->analysis->count(),
            'positive' => $feedbacks->where('analysis.sentiment', 'positive')->count(),
            'neutral' => $feedbacks->where('analysis.sentiment', 'neutral')->count(),
            'negative' => $feedbacks->where('analysis.sentiment', 'negative')->count(),
            'avg_confidence' => $feedbacks->pluck('analysis')
                ->filter()
                ->avg('confidence'),
        ];

        return view('feedbacks.index', compact('feedbacks', 'stats'));
    }

    public function create()
    {
        return view('feedbacks.create');
    }

    public function store(StoreFeedbackRequest $request)
    {
        Feedback::create($request->validated() + [
            'restaurant_id' => auth()->id(),
        ]);

        return redirect()->route('feedbacks.index')->with('success', 'Menu category created successfully.');
    }

    public function edit(Feedback $menuCategory)
    {
        return abort(404);
    }

    public function update(UpdateFeedbackRequest $request, Feedback $menuCategory)
    {
        return abort(404);
    }

    public function destroy(Feedback $menuCategory)
    {
        return abort(404);
    }
}
