<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionPeriod;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('period')->latest()->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $periods = AdmissionPeriod::all();
        return view('admin.announcements.create', compact('periods'));
    }

    public function store(Request $request)
    {
        Announcement::create([
            ...$request->validate([
                'period_id' => ['nullable', 'exists:admission_periods,id'],
                'title' => ['required', 'string', 'max:200'],
                'content' => ['required', 'string'],
                'publish_at' => ['nullable', 'date'],
                'is_published' => ['nullable', 'boolean'],
            ]),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function edit(Announcement $announcement)
    {
        $periods = AdmissionPeriod::all();
        return view('admin.announcements.edit', compact('announcement', 'periods'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $announcement->update($request->validate([
            'period_id' => ['nullable', 'exists:admission_periods,id'],
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'publish_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
        ]));

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
