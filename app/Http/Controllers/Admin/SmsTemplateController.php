<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;

class SmsTemplateController extends Controller
{
    public function index()
    {
        return view('admin.sms.templates', [
            'templates' => SmsTemplate::orderBy('title')->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        SmsTemplate::create($this->validated($request));

        return back()->with('success', 'Template saved.');
    }

    public function update(Request $request, SmsTemplate $template)
    {
        $template->update($this->validated($request));

        return redirect()->route('admin.sms.templates')->with('success', 'Template updated.');
    }

    public function toggle(SmsTemplate $template)
    {
        $template->update(['is_active' => ! $template->is_active]);

        return back()->with('success', $template->is_active ? 'Template switched on.' : 'Template switched off.');
    }

    public function destroy(SmsTemplate $template)
    {
        $template->delete();

        return back()->with('success', 'Template deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:480'],
        ], [], ['body' => 'message']);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
