<?php

namespace App\Http\Controllers\Api\Main;

use App\Models\User;
use App\Models\Registration;
use App\Http\Resources\RegistrationResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $registrations = Registration::with('parent')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return RegistrationResource::collection($registrations);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch registrations: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'wali_nama_depan' => 'required',
            'santri_nama_depan' => 'required',
            'nisn' => 'required',
            'wali_nik' => 'required|min:16|max:16',
        ]);

        DB::beginTransaction();

        try {

            $checkParent = ParentProfile::where('nik', $request->wali_nik)->first();

            if (!$checkParent) {
                // Assuming you have a User model and it is set up correctly
                $user = User::create([
                    'name' => $request->firstName,
                    'email' => $request->email ?? $request->nik,
                    'password' => bcrypt('password'),
                ]);

                $parent = $user->parent()->create([
                    'first_name' => $request->wali_nama_depan,
                    'last_name' => $request->wali_nama_belakang,
                    'nik' => $request->wali_nik,
                    'kk' => $request->wali_kk,
                    'phone' => $request->wali_telepon,
                    'email' => $request->wali_email,
                    'gender' => $request->wali_jenis_kelamin,
                    'parent_as' => $request->wali_sebagai,
                    'card_address' => $request->wali_alamamat_ktp,
                    'domicile_address' => $request->wali_alamat_domisili,
                    'occupation' => $request->wali_pekerjaan_id,
                ]);

                if ($parent) {
                    $user->assignRole('orangtua');
                }
            }

            if ($request->hasFile('dokumen_foto_santri')) {
                $file = $request->file('dokumen_foto_santri');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/registration_files', $fileName, 'public');
            }

            $registration = Registration::create([
                'registration_number' => $this->createRegistNumber(),
                'parent_id' => $checkParent ? $checkParent->nik : $parent->nik,
                'nis' => $request->santri_nisn,
                'first_name' => $request->santri_nama_depan,
                'last_name' => $request->santri_nama_belakang,
                'nik' => $request->santri_nik,
                'gender' => $request->santri_jenis_kelamin,
                'address' => $request->santri_alamat,
                'born_in' => $request->santri_tempat_lahir,
                'born_at' => $request->santri_tanggal_lahir,
                'village_id' => $request->desaId ?? null,
                'photo' => $filePath ?? null,
            ]);

            if ($request->hasFile('dokumen_ijazah')) {
                $ijazahFile = $request->file('dokumen_ijazah');
                $ijazahFileName = time() . '_' . $ijazahFile->getClientOriginalName();
                $ijazahFilePath = $ijazahFile->storeAs('uploads/registration_files', $ijazahFileName, 'public');

                $registration->files()->create([
                    'file_name' => $ijazahFileName,
                    'file_path' => $ijazahFilePath,
                ]);
            }

            if ($request->hasFile('dokumen_opsional')) {
                foreach ($request->file('dokumen_opsional') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('uploads/registration_files', $fileName, 'public');

                    $registration->files()->create([
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                    ]);
                }
            }

            DB::commit();

            return new RegistrationResource('Registration successful', $registration->load('parent'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return new RegistrationResource('Registration failed: ' . $e->getMessage(), null, 500);
        } catch (\Throwable $e) {
            DB::rollBack();
            return new RegistrationResource('An unexpected error occurred: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function createRegistNumber()
    {
        $lastRegistration = Registration::orderBy('created_at', 'desc')->first();
        if (!$lastRegistration) {
            $registrationNumber = 'REG' . date('Y') . '001';
        } else {
            $lastNumber = substr($lastRegistration->registration_number, -3);
            $nextNumber = str_pad((int) $lastNumber + 1, 3, '0', STR_PAD_LEFT);
            $registrationNumber = 'REG' . date('Y') . $nextNumber;
        }
        return $registrationNumber;
    }
}
