<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackTagController extends Controller
{
    public function index(): View
    {
        $tags = FeedbackTag::orderBy('sort_order')->orderBy('label')->get();

        return view('admin.feedback-tags.index', compact('tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'label' => ['required', 'string', 'max:100'],
        ]);

        $maxOrder = FeedbackTag::max('sort_order') ?? 0;

        FeedbackTag::create([
            'label' => $request->label,
            'is_active' => true,
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.feedback-tags.index')
            ->with('success', 'Feedback tag added.');
    }

    public function update(Request $request, FeedbackTag $feedbackTag): RedirectResponse
    {
        $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $feedbackTag->update([
            'label' => $request->label,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? $feedbackTag->sort_order,
        ]);

        return redirect()->route('admin.feedback-tags.index')
            ->with('success', 'Feedback tag updated.');
    }

    public function destroy(FeedbackTag $feedbackTag): RedirectResponse
    {
        $feedbackTag->delete();

        return redirect()->route('admin.feedback-tags.index')
            ->with('success', 'Feedback tag deleted.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        foreach ($request->ids as $order => $id) {
            FeedbackTag::where('id', $id)->update(['sort_order' => $order]);
        }

        return response()->json(['ok' => true]);
    }
}
