<?php

namespace App\Http\Controllers\Api\Main;

use App\Models\User;
use App\Models\Student;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\Registration;
use Illuminate\Http\Request;
use App\Models\ParentProfile;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\RegistrationResource;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\Main\AccountController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;

class RegistrationController extends Controller
{
    /**
     * Menampilkan daftar semua pendaftaran santri
     *
     * Method ini digunakan untuk mengambil semua data pendaftaran santri dari database
     * beserta relasi data orang tua. Data diurutkan berdasarkan tanggal terbaru
     * dan menggunakan pagination.
     *
     * @group Registration
     * @authenticated
     *
     * @queryParam page integer Halaman yang akan ditampilkan. Example: 1
     * @queryParam per_page integer Jumlah data per halaman. Example: 10
     *
     * @response 200 {
     *   "message": "Registrations fetched successfully",
     *   "status": 200,
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "registration_number": "REG2024001",
     *         "first_name": "Ahmad",
     *         "last_name": "Santri",
     *         "nis": "1234567890",
     *         "status": "pending",
     *         "parent": {
     *           "id": 1,
     *           "first_name": "Bapak",
     *           "last_name": "Ahmad",
     *           "nik": "1234567890123456"
     *         },
     *         "created_at": "2024-01-01T00:00:00.000000Z"
     *       }
     *     ],
     *     "total": 50,
     *     "per_page": 10
     *   }
     * }
     */
    public function index()
    {
        try {
            $registrations = Registration::with('parent')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return new RegistrationResource('Registrations fetched successfully', $registrations, 200);
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
            'santri_nisn' => 'required',
            'wali_nik' => 'required|min:16|max:16',
        ]);

        DB::beginTransaction();

        try {

            $checkParent = ParentProfile::where('nik', $request->wali_nik)->first();

            if (!$checkParent) {
                // Assuming you have a User model and it is set up correctly
                $user = User::create([
                    'name' => $request->wali_nama_depan,
                    'email' => $request->wali_email ?? $request->wali_nik,
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
                    'occupation_id' => $request->wali_pekerjaan_id,
                    'education' => $request->wali_pendidikan_id,
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
                'kk' => $request->wali_kk,
                'gender' => $request->santri_jenis_kelamin,
                'address' => $request->santri_alamat,
                'born_in' => $request->santri_tempat_lahir,
                'born_at' => $request->santri_tanggal_lahir,
                'village_id' => $request->desaId ?? null,
                'photo' => $filePath ?? null,
                'previous_school' => $request->pendidikan_sekolah_asal,
                'previous_school_address' => $request->pendidikan_alamat_sekolah,
                'certificate_number' => $request->nomor_ijazah,
                'education_level_id' => $request->education_level_id,
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
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json('Data not found', 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json('An error occurred: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = Registration::with(['parent', 'files', 'occupation'])->findOrFail($id);
            $data->photo_url = Storage::url($data->photo);
            return new RegistrationResource('Data found', $data, 200);
        } catch (\Throwable $th) {
            return response()->json('Data not found: ' . $th->getMessage(), 404);
        }
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
    public function getByCurrentYear()
    {
        try {
            $registrations = Registration::with('parent')
                ->whereYear('created_at', date('Y'))
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return new RegistrationResource('Registrations for the current year fetched successfully', $registrations, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch registrations: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Membuat transaksi pembayaran registrasi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createRegistrationTransaction(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|exists:registrations,id',
            'product_id' => 'required|exists:products,id',
            'hijri_year' => 'required|digits:4',
            'amount' => 'required|numeric',
            'transaction_type_id' => 'required|exists:transaction_types,id',
            'channel' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $registration = Registration::findOrFail($request->registration_id);

            // Create student
            $student = Student::create([
                'parent_id' => $registration->nik,
                'nis' => $this->generateNis($request->hijri_year),
                'period' => $request->hijri_year,
                'first_name' => $registration->first_name,
                'last_name' => $registration->last_name,
                'gender' => $registration->gender,
                'address' => $registration->address,
                'born_in' => $registration->born_in,
                'born_at' => $registration->born_at,
                'village_id' => $registration->village_id,
                'photo' => $registration->photo,
                'user_id' => auth()->user()->id,
                'education_type_id' => $registration->education_level_id,
                'status' => 'Tidak Aktif', // Default status
            ]);

            // Create account
            $accountController = new AccountController();
            $accountRequest = new Request([
                'student_id' => $student->id,
                'product_id' => $request->product_id,
            ]);
            $accountResponse = $accountController->store($accountRequest);
            $account = json_decode($accountResponse->getContent(), true);

            if ($accountResponse->getStatusCode() != 201) {
                DB::rollBack();
                return response()->json(['message' => 'Failed to create account', 'errors' => $account], 500);
            }

            // Create transaction
            $transaction = Transaction::create([
                'id' => Str::uuid(),
                'transaction_type_id' => $request->transaction_type_id,
                'description' => 'Biaya Pendaftaran',
                'amount' => $request->amount,
                'status' => 'PENDING',
                'reference_number' => $registration->registration_number,
                'channel' => $request->channel,
                'source_account' => $account['account_number'],
                'destination_account' => null,
            ]);

            if (!$transaction) {
                DB::rollBack();
                return response()->json(['message' => 'Failed to create transaction', 'errors' => $transaction], 500);
            }

            DB::commit();

            return response()->json($transaction, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaksi Pendaftaran Gagal', [
                'error' => $e->getMessage(),
                'registration_id' => $request->registration_id,
            ]);
            return response()->json(['message' => 'Gagal membuat transaksi pendaftaran', 'error' => $e->getMessage()], 500);
        }
    }

    private function generateNis($hijriYear)
    {
        $prefix = $hijriYear . '0197';
        $lastStudent = Student::where('nis', 'like', $prefix . '%')->orderBy('nis', 'desc')->first();

        if ($lastStudent) {
            $lastSequence = (int) substr($lastStudent->nis, -3);
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }

        return $prefix . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
    }
}
