<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceScanController extends Controller
{
    public function scanForm()
    {
        $today = Attendance::with('attendee')->whereDate('date', today())->latest('check_in_at')->limit(10)->get();

        return view('guru.attendance.scan', compact('today'));
    }

    public function scan(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'max:20']]);

        $result = Attendance::recordScan($request->input('code'), $request->user());

        if ($result['ok']) {
            return back()->with('success', $result['message']);
        }

        return back()->withErrors(['code' => $result['message']]);
    }
}
