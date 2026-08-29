<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\FeedbackAnalysis;
use App\Services\AI\MlSentimentAnalysisService;
use Illuminate\Http\Request;

class MlAnalysisController extends Controller
{
    public function analyzeSingle(Request $request, $feedbackId, MlSentimentAnalysisService $service)
    {
        $feedback = Feedback::findOrFail($feedbackId);
        $analysis = $service->analyze($feedback);

        return response()->json(['data' => $analysis]);
    }

    public function analytics(Request $request)
    {
        $analyses = FeedbackAnalysis::query()
            ->when($request->start_date, fn ($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->end_date, fn ($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->get();

        $stats = [
            'total' => $analyses->count(),
            'sentiment' => [
                'positive' => $analyses->where('sentiment', 'positive')->count(),
                'neutral' => $analyses->where('sentiment', 'neutral')->count(),
                'negative' => $analyses->where('sentiment', 'negative')->count(),
            ],
            'average_confidence' => $analyses->avg('confidence'),
        ];

        return response()->json(['data' => $stats]);
    }

    public function feedbackWithAnalysis(Request $request)
    {
        $feedbacks = Feedback::with('analysis')
            ->when($request->sentiment, fn ($q) => $q->whereHas('analysis', fn ($sq) => $sq->where('sentiment', $request->sentiment)))
            ->paginate($request->per_page ?? 20);

        return response()->json($feedbacks);
    }
}
