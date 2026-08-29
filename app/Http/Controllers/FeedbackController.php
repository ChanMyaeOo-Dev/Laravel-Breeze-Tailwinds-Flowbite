<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Http\Requests\UpdateFeedbackRequest;
use App\Models\Feedback;
use App\Services\AI\FeedbackSummaryService;
use App\Traits\RestaurantScoped;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    use RestaurantScoped;

    public function index(FeedbackSummaryService $summaryService)
    {
        $feedbacks = Feedback::with('analysis')
            ->where('restaurant_id', Auth::id())
            ->latest()
            ->get();

        $analyzed = $feedbacks->filter->analysis;

        // $stats = [
        //     'total' => $feedbacks->count(),
        //     'analyzed' => $analyzed->count(),
        //     'positive' => $analyzed->where('sentiment', 'positive')->count(),
        //     'neutral' => $analyzed->where('sentiment', 'neutral')->count(),
        //     'negative' => $analyzed->where('sentiment', 'negative')->count(),
        //     'avg_confidence' => $analyzed->pluck('confidence')->avg(),
        //     'avg_rating' => round($feedbacks->avg('rating'), 1),
        // ];
        $stats = [
            'total' => $feedbacks->count(),

            'analyzed' => $analyzed->count(),

            'positive' => $analyzed->filter(
                fn($feedback) => $feedback->analysis?->sentiment === 'positive'
            )->count(),

            'neutral' => $analyzed->filter(
                fn($feedback) => $feedback->analysis?->sentiment === 'neutral'
            )->count(),

            'negative' => $analyzed->filter(
                fn($feedback) => $feedback->analysis?->sentiment === 'negative'
            )->count(),

            'avg_confidence' => round(
                $analyzed->avg(fn($feedback) => $feedback->analysis?->confidence),
                2
            ),

            'avg_rating' => round($feedbacks->avg('rating'), 1),
        ];

        $summary = $summaryService->generate($feedbacks);
        return view('feedbacks.index', compact('feedbacks', 'stats', 'summary'));
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
