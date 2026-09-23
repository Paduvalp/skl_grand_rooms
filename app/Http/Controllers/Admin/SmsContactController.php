<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsContact;
use App\Support\ContactImport;
use Illuminate\Http\Request;

class SmsContactController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));

        $contacts = SmsContact::query()
            ->when($search !== '', function ($query) use ($search) {
                $digits = preg_replace('/\D/', '', $search) ?? '';

                $query->where(function ($q) use ($search, $digits) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('note', 'like', '%'.$search.'%');

                    if ($digits !== '') {
                        $q->orWhere('phone', 'like', '%'.$digits.'%');
                    }
                });
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.sms.contacts', [
            'contacts' => $contacts,
            'search' => $search,
            'total' => SmsContact::count(),
            'activeTotal' => SmsContact::active()->count(),
        ]);
    }

    /** Add one number by hand. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],
            'note' => ['nullable', 'string', 'max:190'],
        ]);

        $phone = SmsContact::normalise($data['phone']);

        if (! $phone) {
            return back()->withInput()->with('error', 'That is not a valid Indian mobile number.');
        }

        if (SmsContact::where('phone', $phone)->exists()) {
            return back()->withInput()->with('error', $phone.' is already in the list.');
        }

        SmsContact::create([
            'name' => $data['name'] ?? null,
            'phone' => $phone,
            'note' => $data['note'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Number added.');
    }

    /** Read a CSV or Excel file and add everything in it. */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:5120'],
        ], [
            'file.mimes' => 'Upload a CSV or Excel (.xlsx) file. In Excel use File > Save As and pick CSV or .xlsx.',
            'file.max' => 'The file must be 5 MB or smaller.',
        ]);

        $file = $request->file('file');
        $rows = ContactImport::rows($file->getRealPath(), $file->getClientOriginalExtension());

        if (! $rows) {
            return back()->with('error', 'No mobile numbers were found in that file. It needs a column of 10-digit numbers.');
        }

        $added = 0;
        $updated = 0;
        $skipped = 0;
        $seen = [];

        foreach ($rows as $row) {
            // The same number twice in one file counts once.
            if (isset($seen[$row['phone']])) {
                $skipped++;

                continue;
            }

            $seen[$row['phone']] = true;
            $existing = SmsContact::where('phone', $row['phone'])->first();

            if ($existing) {
                // Fill in a name we did not have before, but never wipe one.
                if ($row['name'] && ! $existing->name) {
                    $existing->update(['name' => $row['name']]);
                    $updated++;
                } else {
                    $skipped++;
                }

                continue;
            }

            SmsContact::create([
                'name' => $row['name'],
                'phone' => $row['phone'],
                'is_active' => true,
            ]);

            $added++;
        }

        $message = "{$added} added";
        $message .= $updated ? ", {$updated} updated" : '';
        $message .= $skipped ? ", {$skipped} already in the list" : '';

        return back()->with('success', 'Import finished: '.$message.'.');
    }

    /**
     * A small example file, so nobody has to guess the column order.
     * CSV opens straight in Excel.
     */
    public function sample()
    {
        $rows = [
            ['Name', 'Phone'],
            ['Ravi Kumar', '9876543210'],
            ['Anita Sharma', '9845012345'],
            ['Mohammed Faizal', '+91 90080 77665'],
        ];

        $csv = '';

        foreach ($rows as $row) {
            $csv .= implode(',', $row)."\r\n";
        }

        // The marker at the front tells Excel the file is UTF-8, so names
        // with Indian spellings do not arrive as question marks.
        return response("\xEF\xBB\xBF".$csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="sms-numbers-sample.csv"',
        ]);
    }

    public function toggle(SmsContact $contact)
    {
        $contact->update(['is_active' => ! $contact->is_active]);

        return back()->with('success', $contact->is_active ? 'Number switched on.' : 'Number switched off.');
    }

    public function destroy(SmsContact $contact)
    {
        $contact->delete();

        return back()->with('success', 'Number removed.');
    }

    /**
     * Take the ticked numbers over to the send form, ready to go.
     */
    public function useForSms(Request $request)
    {
        $ids = (array) $request->input('ids', []);

        $numbers = SmsContact::active()->whereIn('id', $ids)->pluck('phone')->all();

        if (! $numbers) {
            return back()->with('error', 'Tick at least one number first.');
        }

        return redirect()->route('admin.sms.create')
            ->withInput(['numbers' => implode(', ', $numbers)])
            ->with('success', count($numbers).' '.\Illuminate\Support\Str::plural('number', count($numbers)).' ready. Write your message and send.');
    }
}
