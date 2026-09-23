<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsContact;
use App\Models\SmsMessage;
use App\Models\SmsTemplate;
use App\Support\SmsGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmsController extends Controller
{
    public function create(SmsGateway $sms)
    {
        return view('admin.sms.send', [
            'usedToday' => $sms->usedToday(),
            'dailyLimit' => $sms->limit(),
            'mode' => $sms->mode(),
            'configured' => $sms->isConfigured(),
            'history' => SmsMessage::with('user')->latest('id')->paginate(10),
            'templates' => SmsTemplate::active()->orderBy('title')->get(),
        ]);
    }

    public function send(Request $request, SmsGateway $sms)
    {
        $data = $request->validate([
            'numbers' => ['required', 'string', 'max:5000'],
            'message' => ['required', 'string', 'max:480'],
        ], [], ['numbers' => 'phone numbers']);

        $numbers = $this->cleanNumbers($data['numbers']);

        if (! $numbers) {
            return back()->withInput()
                ->with('error', 'No valid 10-digit numbers. Write them as 9876543210, separated by commas or one per line.');
        }

        // A message with {name} in it is written fresh for each guest, so
        // each one gets their own text. Everything else goes out as one
        // message to the whole list.
        $result = SmsTemplate::needsName($data['message'])
            ? $this->sendByName($sms, $numbers, $data['message'])
            : $sms->send($numbers, $data['message']);

        // Kept whether it worked or not, so there is always a record of
        // what was sent to whom, and of what went wrong when it failed.
        $this->record($sms, $numbers, $data['message'], $result);

        if (! $result['ok']) {
            return back()->withInput()->with('error', $result['error']);
        }

        $count = count($numbers);

        return redirect()->route('admin.sms.create')->with(
            'success',
            $count === 1
                ? 'Message sent to '.$numbers[0].'.'
                : "Message sent to {$count} numbers."
        );
    }

    /**
     * Send one text per guest, each with their own name in it.
     *
     * Names come from the saved phone numbers. A number nobody has named
     * gets "there", so the message still reads properly.
     */
    private function sendByName(SmsGateway $sms, array $numbers, string $template): array
    {
        $names = SmsContact::whereIn('phone', $numbers)->pluck('name', 'phone');

        $sent = 0;
        $errors = [];

        foreach ($numbers as $number) {
            $result = $sms->send([$number], SmsTemplate::withName($template, $names[$number] ?? null));

            if ($result['ok']) {
                $sent++;

                continue;
            }

            $errors[] = $number.': '.$result['error'];

            // A limit reached or a phone switched off will stop the rest
            // too, so there is no point hammering the gateway.
            if (count($errors) >= 3) {
                break;
            }
        }

        if ($sent === 0) {
            return ['ok' => false, 'error' => $errors[0] ?? 'Nothing could be sent.'];
        }

        if ($errors) {
            return [
                'ok' => false,
                'error' => "Sent to {$sent}, but some failed - ".implode('; ', $errors),
                'partial' => true,
                'sent' => $sent,
            ];
        }

        return ['ok' => true, 'used_today' => $sms->usedToday()];
    }

    /** Write the attempt into the sms_messages table. */
    private function record(SmsGateway $sms, array $numbers, string $message, array $result): void
    {
        $parts = $sms->parts($message);

        SmsMessage::create([
            'message' => $message,
            'numbers' => implode(', ', $numbers),
            'recipients' => count($numbers),
            'parts' => $parts,
            'cost' => count($numbers) * $parts,
            'status' => $result['ok'] ? 'sent' : 'failed',
            'error' => $result['ok'] ? null : ($result['error'] ?? null),
            'mode' => $sms->mode(),
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Turn whatever was typed into the box into Indian mobile numbers.
     *
     * Splits on commas, spaces and new lines, throws away anything that is
     * not a real 10 digit number, and keeps the last 10 digits so that
     * 09876543210 and +91 98765 43210 both come out the same.
     *
     * @return array<int, string>
     */
    private function cleanNumbers(string $input): array
    {
        $numbers = [];

        foreach (preg_split('/[\s,;]+/', $input) ?: [] as $entry) {
            $digits = preg_replace('/\D/', '', $entry) ?? '';

            if (strlen($digits) < 10) {
                continue;
            }

            $numbers[] = '+91'.substr($digits, -10);
        }

        return array_values(array_unique($numbers));
    }
}
