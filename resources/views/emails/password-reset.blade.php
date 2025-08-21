<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }
        .header, .footer {
            background-color: #e0f2fe; /* Light Blue */
            padding: 20px;
            text-align: center;
            color: #0c4a6e; /* Darker Blue Text */
        }
        .header h1, .footer p {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
            color: #333333;
        }
        .content p {
            margin: 0 0 15px;
        }
        .button {
            display: inline-block;
            background-color: #0ea5e9; /* Sky Blue */
            color: #ffffff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer p {
            font-size: 14px;
        }
</style>
</head>
<body>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <div class="container">
                    <div class="header">
                        <h1>{{ config('app.name', 'Sistem Manajemen Pesantren') }}</h1>
                    </div>
                    <div class="content">
                        <h2>Halo!</h2>
                        <p>Anda menerima email ini karena kami menerima permintaan pengaturan ulang kata sandi untuk akun Anda.</p>
                        <p>Silakan klik tombol di bawah ini untuk mereset kata sandi Anda:</p>
                        <p style="text-align: center;">
                            <a href="{{ url('password/reset', $token) }}" class="button">Reset Kata Sandi</a>
                        </p>
                        <p>Tautan pengaturan ulang kata sandi ini akan kedaluwarsa dalam {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} menit.</p>
                        <p>Jika Anda tidak merasa meminta pengaturan ulang kata sandi, Anda dapat mengabaikan email ini.</p>
                        <p>Terima kasih,<br>Tim {{ config('app.name', 'Sistem Manajemen Pesantren') }}</p>
                    </div>
                    <div class="footer">
                        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Sistem Manajemen Pesantren') }}. Semua hak cipta dilindungi.</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
