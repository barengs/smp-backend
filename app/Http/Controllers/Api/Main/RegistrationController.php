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
            'first_name' => 'required',
            'nis' => 'required',
            'nik' => 'required|min:16|max:16',
        ]);

        DB::beginTransaction();

        try {

            $checkParent = ParentProfile::where('nik', $request->nik)->first();

            if (!$checkParent) {
                // Assuming you have a User model and it is set up correctly
                $user = User::create([
                    'name' => $request->firstName,
                    'email' => $request->email ?? $request->nik,
                    'password' => bcrypt('password'),
                ]);

                $parent = $user->parent()->create([
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'nik' => $request->nik,
                    'kk' => $request->kk,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'gender' => $request->gender,
                    'parent_as' => $request->parentAs,
                    'card_address' => $request->alamamatKtp,
                    'domicile_address' => $request->alamatDomisili,
                    'occupation' => $request->pekerjaanValue,
                ]);

                if ($parent) {
                    $user->assignRole('orangtua');
                }
            }

            if ($request->hasFile('fotoSantri')) {
                $file = $request->file('fotoSantri');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/registration_files', $fileName, 'public');
            }

            $registration = Registration::create([
                'registration_number' => $this->createRegistNumber(),
                'parent_id' => $checkParent ? $checkParent->nik : $parent->nik,
                'nis' => $request->nisn,
                'first_name' => $request->firstNameSantri,
                'last_name' => $request->lastNameSantri,
                'nik' => $request->nikSantri,
                'gender' => $request->jenisKelamin,
                'address' => $request->alamatSantri,
                'born_in' => $request->tempatLahir,
                'born_at' => $request->tanggalLahir,
                'village_id' => $request->desaId ?? null,
                'photo' => $filePath ?? null,
            ]);

            if ($request->hasFile('ijazahFile')) {
                $ijazahFile = $request->file('ijazahFile');
                $ijazahFileName = time() . '_' . $ijazahFile->getClientOriginalName();
                $ijazahFilePath = $ijazahFile->storeAs('uploads/registration_files', $ijazahFileName, 'public');

                $registration->files()->create([
                    'file_name' => $ijazahFileName,
                    'file_path' => $ijazahFilePath,
                ]);
            }

            if ($request->hasFile('optionalDocuments')) {
                foreach ($request->file('optionalDocuments') as $file) {
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
