<x-emails.layouts.base
    title="Konfirmasi Pembatalan Ruang Meeting Anda!"
    accentColor="#fd7e14"
>
    <p style="margin:0 0 16px; font-size:15px; color:#343a40; line-height:1.6;">
        Yth. <strong>{{ $booking->user?->Name ?? 'Pengguna' }}</strong>,
    </p>

    <p style="margin:0 0 20px; font-size:15px; color:#343a40; line-height:1.6;">
        Dengan menyesal kami informasikan bahwa pemesanan ruang meeting Anda telah <strong>dibatalkan</strong>.
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
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600; border-bottom:1px solid #f1f3f5;">{{ $booking->location ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px; font-size:14px; color:#6c757d;">Keperluan</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600;">{{ $booking->description ?? '-' }}</td>
        </tr>
    </table>

    <p style="margin:0; font-size:14px; color:#6c757d; line-height:1.6; text-align:center;">
        Terima kasih telah menggunakan layanan kami.
    </p>
</x-emails.layouts.base>
