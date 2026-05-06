<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(): View
    {
        $feedbacks = Feedback::query()
            ->with(['artifact.category'])
            ->latest()
            ->paginate(12);

        return view('admin.feedbacks.index', compact('feedbacks'));
    }
}
