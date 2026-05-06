<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    // GET /api/feedback  — admin list
    public function index()
    {
        $feedbacks = Feedback::with('artifact:id,name')
            ->orderByDesc('created_at')
            ->get();
        return response()->json($feedbacks);
    }

    // POST /api/feedback  — public submit
    public function store(Request $request)
    {
        $request->validate([
            'artifact_id' => 'nullable|exists:artifacts,id',
            'rating'      => 'required|integer|min:1|max:5',
            'message'     => 'nullable|string',
        ]);

        $feedback = Feedback::create([
            'artifact_id' => $request->artifact_id,
            'rating'      => $request->rating,
            'message'     => $request->message ?? '',
        ]);

        return response()->json([
            'message'  => 'Feedback enregistré.',
            'feedback' => $feedback,
        ], 201);
    }
}

