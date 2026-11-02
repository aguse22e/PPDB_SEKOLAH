<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    /** Hasil seleksi milik pendaftar yang sedang login. */
    public function show(Request $request, Application $application)
    {
        abort_unless($application->applicant_id === $request->user()->applicant?->id, 403);

        $application->load(['period', 'path', 'major', 'selectionResults']);

        return view('applicant.results.show', compact('application'));
    }
}
