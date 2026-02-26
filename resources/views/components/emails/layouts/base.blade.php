<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f9; font-family: Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9; padding: 30px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">

                {{-- Header --}}
                <tr>
                    <td style="background-color:{{ $accentColor ?? '#007bff' }}; padding: 0;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding: 28px 32px;">
                                    <p style="margin:0; font-size:13px; color:rgba(255,255,255,0.8); text-transform:uppercase; letter-spacing:1px;">{{ config('app.name') }}</p>
                                    <h1 style="margin:6px 0 0; font-size:22px; font-weight:700; color:#ffffff; line-height:1.3;">{{ $title ?? 'Notifikasi Pemesanan' }}</h1>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Body --}}
                <tr>
                    <td style="padding: 32px;">
                        {{ $slot }}
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="background-color:#f8f9fa; border-top: 1px solid #e9ecef; padding: 20px 32px;">
                        <p style="margin:0; font-size:14px; color:#6c757d; line-height:1.6;">
                            Salam Hangat,<br>
                            <strong style="color:#495057;">General Services Department</strong>
                        </p>
                        <p style="margin:12px 0 0; font-size:11px; color:#adb5bd;">
                            Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
