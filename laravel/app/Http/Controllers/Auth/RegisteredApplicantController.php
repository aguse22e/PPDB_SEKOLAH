<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterApplicantRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisteredApplicantController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Satu proses membuat dua data sekaligus dalam satu transaksi:
     * akun users (role applicant) + profil applicants.
     */
    public function store(RegisterApplicantRequest $request)
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            $role = Role::where('code', 'applicant')->firstOrFail();

            $user = User::create([
                'role_id' => $role->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'is_active' => true,
            ]);

            $user->applicant()->create([
                'full_name' => $data['full_name'],
                'gender' => $data['gender'],
                'birth_date' => $data['birth_date'],
                'nisn' => $data['nisn'] ?? null,
                'previous_school' => $data['previous_school'] ?? null,
                'phone' => $data['phone'] ?? null,
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()
            ->route('applicant.dashboard')
            ->with('success', 'Akun berhasil dibuat. Lengkapi biodata dan data orang tua/wali, lalu buat pendaftaran.');
    }
}
