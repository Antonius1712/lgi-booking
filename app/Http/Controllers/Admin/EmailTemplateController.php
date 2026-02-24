<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailTemplateController extends Controller
{
    public function index(): View
    {
        $templates = EmailTemplate::orderBy('email_type')->get();

        $allTypes = EmailTemplate::allTypes();

        return view('admin.email-templates.index', compact('templates', 'allTypes'));
    }

    public function edit(string $trigger): View
    {
        $template = EmailTemplate::where('email_type', $trigger)->first();

        return view('admin.email-templates.edit', compact('template', 'trigger'));
    }

    public function update(Request $request, string $trigger): RedirectResponse
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'email_body' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        EmailTemplate::updateOrCreate(
            ['email_type' => $trigger],
            [
                'subject' => $request->subject,
                'email_body' => $request->email_body,
                'is_active' => $request->boolean('is_active'),
            ],
        );

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Email template updated successfully.');
    }
}
