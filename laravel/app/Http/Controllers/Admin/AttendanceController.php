<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        // Validasi filter supaya nilai tanggal/status/tipe yang tidak valid
        // tidak lolos ke query (tanggal tidak valid sebelumnya membuat error 500).
        $filters = $request->validate([
            'date' => ['nullable', 'date'],
            'type' => ['nullable', 'in:siswa,guru'],
            'status' => ['nullable', 'in:'.implode(',', Attendance::STATUSES)],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $date = $filters['date'] ?? today()->toDateString();
        $type = $filters['type'] ?? null;
        $status = $filters['status'] ?? null;
        $search = trim((string) ($filters['search'] ?? ''));

        $query = Attendance::with('attendee')->whereDate('date', $date);

        if ($type === 'siswa') {
            $query->where('attendee_type', Applicant::class);
        } elseif ($type === 'guru') {
            $query->where('attendee_type', User::class);
        }

        if (in_array($status, Attendance::STATUSES, true)) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHasMorph('attendee', [Applicant::class], fn ($q2) => $q2->where('full_name', 'like', '%'.$search.'%'))
                    ->orWhereHasMorph('attendee', [User::class], fn ($q2) => $q2->where('name', 'like', '%'.$search.'%'));
            });
        }

        $attendances = $query->latest('check_in_at')->paginate(20)->withQueryString();

        $summary = Attendance::whereDate('date', $date)
            ->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $students = Attendance::registeredStudents()->get();
        $teachers = Attendance::teachers()->get();

        return view('admin.attendance.index', compact('attendances', 'summary', 'date', 'type', 'status', 'search', 'students', 'teachers'));
    }

    public function scanForm()
    {
        $today = Attendance::with('attendee')->whereDate('date', today())->latest('check_in_at')->limit(10)->get();

        return view('admin.attendance.scan', compact('today'));
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

    /** Catat manual: izin / sakit / alpa (atau hadir tanpa scan). */
    public function storeManual(Request $request)
    {
        $data = $request->validate([
            'attendee' => ['required', 'string', 'regex:/^(siswa|guru):\d+$/'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:'.implode(',', Attendance::STATUSES)],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        [$type, $id] = explode(':', $data['attendee']);
        $class = $type === 'siswa' ? Applicant::class : User::class;
        $person = $class::findOrFail($id);

        Attendance::updateOrCreate(
            ['attendee_type' => $class, 'attendee_id' => $person->id, 'date' => $data['date']],
            [
                'status' => $data['status'],
                'method' => 'manual',
                'scanned_by' => $request->user()->id,
                'notes' => $data['notes'],
            ]
        );

        $name = $person->full_name ?? $person->name;

        return back()->with('success', 'Absensi manual untuk '.$name.' tanggal '.$data['date'].' dicatat ('.$data['status'].').');
    }

    public function update(Request $request, Attendance $attendance)
    {
        $attendance->update($request->validate([
            'status' => ['required', 'in:'.implode(',', Attendance::STATUSES)],
            'notes' => ['nullable', 'string', 'max:255'],
        ]));

        return back()->with('success', 'Status absensi diperbarui.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return back()->with('success', 'Data absensi dihapus.');
    }

    /** Daftar kartu barcode siswa terdaftar dan guru. */
    public function cards()
    {
        $students = Attendance::registeredStudents()->get();
        $teachers = Attendance::teachers()->get();

        return view('admin.attendance.cards', compact('students', 'teachers'));
    }

    /** Buat kode barcode untuk siswa/guru lama yang belum punya. */
    public function generateMissing()
    {
        $students = Applicant::whereNull('barcode_code')->get();
        foreach ($students as $student) {
            $student->updateQuietly(['barcode_code' => 'SIS'.str_pad((string) $student->id, 6, '0', STR_PAD_LEFT)]);
        }

        $teachers = User::whereNull('barcode_code')
            ->whereHas('role', fn ($q) => $q->where('code', 'guru'))->get();
        foreach ($teachers as $teacher) {
            $teacher->updateQuietly(['barcode_code' => 'GRU'.str_pad((string) $teacher->id, 6, '0', STR_PAD_LEFT)]);
        }

        $total = $students->count() + $teachers->count();

        return back()->with('success', 'Kode barcode dibuat untuk '.$total.' kartu ('.$students->count().' siswa, '.$teachers->count().' guru).');
    }
}
