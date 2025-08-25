<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword', 'resetPassword']]);
    }

    /**
     * Mendaftarkan user baru ke sistem
     *
     * Method ini digunakan untuk mendaftarkan user baru ke sistem pesantren.
     * User yang didaftarkan akan memiliki akses ke sistem sesuai dengan role yang diberikan.
     *
     * @group Authentication
     *
     * @bodyParam name string required Nama lengkap user. Example: Ahmad Santri
     * @bodyParam email string required Email user (harus unik). Example: ahmad@example.com
     * @bodyParam password string required Password user (minimal 6 karakter). Example: password123
     *
     * @response 201 {
     *   "user": {
     *     "id": 1,
     *     "name": "Ahmad Santri",
     *     "email": "ahmad@example.com",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "email": ["The email has already been taken."],
     *   "password": ["The password must be at least 6 characters."]
     * }
     */
    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
        ]);

        return response()->json(['user' => $user], 201);
    }

    /**
     * Login user dan mendapatkan token JWT
     *
     * Method ini digunakan untuk autentikasi user dan mendapatkan token JWT
     * yang akan digunakan untuk mengakses endpoint yang memerlukan autentikasi.
     *
     * @group Authentication
     *
     * @bodyParam email string required Email user. Example: ahmad@example.com
     * @bodyParam password string required Password user. Example: password123
     *
     * @response 200 {
     *   "user": {
     *     "id": 1,
     *     "name": "Ahmad Santri",
     *     "email": "ahmad@example.com",
     *     "role": ["santri"]
     *   },
     *   "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
     *   "token_type": "bearer",
     *   "expires_in": 3600
     * }
     *
     * @response 401 {
     *   "error": "Unauthorized"
     * }
     */
    public function login()
    {
        $validator = Validator::make(request()->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $loginType = filter_var(request('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $loginType => request('login'),
            'password' => request('password')
        ];

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Mendapatkan data profile user yang sedang login
     *
     * Method ini digunakan untuk mendapatkan data lengkap user yang sedang login,
     * termasuk role dan profile yang terkait (parent atau employee).
     *
     * @group Authentication
     * @authenticated
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Ahmad Santri",
     *     "email": "ahmad@example.com",
     *     "role": ["santri"],
     *     "profile": {
     *       "id": 1,
     *       "student_id": "STU001",
     *       "name": "Ahmad Santri",
     *       "status": "Aktif"
     *     }
     *   }
     * }
     *
     * @response 401 {
     *   "error": "Unauthorized"
     * }
     */
    public function profile()
    {
        $user = auth()->user();
        $role = $user->getRoleNames(); // Get user roles
        if ($role[0] == 'orangtua') {
            $user->profile = $user->parent; // Attach parent profile if user is a parent
        } else {
            $user->profile = $user->staff; // Attach employee profile if user is a teacher
        }
        // $user->profile = $user->profile(); // Attach profile based on role
        return response()->json(['data' => $user]);
    }

    /**
     * Logout user dan invalidate token JWT
     *
     * Method ini digunakan untuk logout user dengan menghapus token JWT
     * yang sedang aktif. Token yang dihapus tidak dapat digunakan lagi.
     *
     * @group Authentication
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Logout Berhasil!"
     * }
     *
     * @response 400 {
     *   "success": false,
     *   "message": "Logout gagal, token tidak dapat dihapus."
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Terjadi kesalahan saat logout.",
     *   "error": "Error details"
     * }
     */
    public function logout()
    {
        //remove token
        try {
            $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

            if ($removeToken) {
                //return response JSON
                return response()->json([
                    'success' => true,
                    'message' => 'Logout Berhasil!',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Logout gagal, token tidak dapat dihapus.',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat logout.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengirim email reset password
     *
     * Method ini digunakan untuk mengirim email reset password ke user.
     *
     * @group Authentication
     *
     * @bodyParam email string required Email user. Example: ahmad@example.com
     *
     * @response 200 {
     *   "message": "Password reset link sent to your email."
     * }
     *
     * @response 404 {
     *   "message": "User not found."
     * }
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $token = Str::random(60);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        // Kirim email dengan link reset password
        Mail::to($user->email)->send(new \App\Mail\ResetPasswordMail($token));

        return response()->json(['message' => 'Password reset link sent to your email.']);
    }

    /**
     * Mereset password user
     *
     * Method ini digunakan untuk mereset password user dengan token yang valid.
     *
     * @group Authentication
     *
     * @bodyParam token string required Token reset password.
     * @bodyParam email string required Email user.
     * @bodyParam password string required Password baru user.
     * @bodyParam password_confirmation string required Konfirmasi password baru.
     *
     * @response 200 {
     *   "message": "Password has been reset."
     * }
     *
     * @response 404 {
     *   "message": "Invalid token."
     * }
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return response()->json(['message' => 'Invalid token.'], 404);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password has been reset.']);
    }

    /**
     * Mengganti password user yang sedang login
     *
     * Method ini digunakan untuk mengganti password user yang sedang login.
     *
     * @group Authentication
     * @authenticated
     *
     * @bodyParam current_password string required Password saat ini.
     * @bodyParam new_password string required Password baru.
     * @bodyParam new_password_confirmation string required Konfirmasi password baru.
     *
     * @response 200 {
     *   "message": "Password changed successfully."
     * }
     *
     * @response 400 {
     *   "message": "Current password does not match."
     * }
     *
     * @response 422 {
     *   "new_password": ["The new password confirmation does not match."]
     * }
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:6',
        ]);

        $user = Auth::user();

        if (!\Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password does not match.'], 400);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully.']);
    }

    /**
     * Refresh token JWT yang sedang aktif
     *
     * Method ini digunakan untuk memperbarui token JWT yang sedang aktif
     * tanpa perlu login ulang. Token baru akan memiliki masa berlaku yang baru.
     *
     * @group Authentication
     * @authenticated
     *
     * @response 200 {
     *   "user": {
     *     "id": 1,
     *     "name": "Ahmad Santri",
     *     "email": "ahmad@example.com",
     *     "role": ["santri"]
     *   },
     *   "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
     *   "token_type": "bearer",
     *   "expires_in": 3600
     * }
     *
     * @response 401 {
     *   "error": "Unauthorized"
     * }
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user = auth()->user();
        $user->role = $user->getRoleNames();
        $user->profile = $user->userProfile();
        return response()->json([
            'user' => $user,
            // 'role' => $user->getRoleNames(),
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

}
