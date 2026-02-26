<x-emails.layouts.base
    title="Konfirmasi Pemesanan Ruang Meeting Berhasil!"
    accentColor="#fd7e14"
>
    <p style="margin:0 0 16px; font-size:15px; color:#343a40; line-height:1.6;">
        Yth. <strong>{{ $booking->user?->Name ?? 'Pengguna' }}</strong>,
    </p>

    <p style="margin:0 0 20px; font-size:15px; color:#343a40; line-height:1.6;">
        Kami menginformasikan bahwa pemesanan ruang meeting Anda dengan detail berikut telah <strong>berhasil</strong>:
    </p>

    {{-- Booking Detail Table --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:24px; border-radius:6px; overflow:hidden; border: 1px solid #dee2e6;">
        <tr style="background-color:#f8f9fa;">
            <td style="padding:12px 16px; font-size:13px; font-weight:700; color:#495057; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #dee2e6;" colspan="2">Detail Pemesanan</td>
        </tr>
        <tr>
            <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5; width:40%;">Nama Ruang Meeting</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600; border-bottom:1px solid #f1f3f5;">{{ $booking->meetingRoom?->name ?? '-' }}</td>
        </tr>
        <tr style="background-color:#fafafa;">
            <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5;">Nama Pemohon</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600; border-bottom:1px solid #f1f3f5;">
                {{ $booking->user?->Name ?? '-' }} / {{ $booking->user?->NIK ?? '-' }}
            </td>
        </tr>
        <tr>
            <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5;">Tanggal & Waktu</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600; border-bottom:1px solid #f1f3f5;">
                {{ \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('d M Y') }},
                pukul {{ \Carbon\Carbon::parse($booking->start_time)->format('H.i') }}
                s/d {{ \Carbon\Carbon::parse($booking->end_time)->format('H.i') }} WIB
            </td>
        </tr>
        <tr style="background-color:#fafafa;">
            <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5;">Lokasi</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600; border-bottom:1px solid #f1f3f5;">
                {{ $booking->location ? str($booking->location)->title() : '-' }}
            </td>
        </tr>
        <tr>
            <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5;">Jenis Penggunaan</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600; border-bottom:1px solid #f1f3f5;">{{ $booking->usage_type ?? '-' }}</td>
        </tr>
        <tr style="background-color:#fafafa;">
            <td style="padding:12px 16px; font-size:14px; color:#6c757d;">Keperluan</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600;">{{ $booking->description ?? '-' }}</td>
        </tr>
    </table>

    {{-- Info Box --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff3cd; border:1px solid #ffc107; border-radius:6px; margin-bottom:24px;">
        <tr>
            <td style="padding:14px 16px;">
                <p style="margin:0; font-size:13px; color:#856404; font-weight:700;">&#9432; Informasi Penting</p>
                <p style="margin:6px 0 0; font-size:13px; color:#856404; line-height:1.6;">
                    Mohon konfirmasi jika ada perubahan rencana. Anda dapat membatalkan atau mengubah pesanan melalui tautan di bawah ini.
                </p>
            </td>
        </tr>
    </table>

    {{-- CTA Button --}}
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <a href="{{ route('home') }}"
                   style="display:inline-block; background-color:#28a745; color:#ffffff; text-decoration:none; padding:12px 28px; border-radius:6px; font-size:14px; font-weight:700;">
                    Kelola Pemesanan
                </a>
            </td>
        </tr>
    </table>
</x-emails.layouts.base>
