<x-emails.layouts.base
    title="Konfirmasi Perjalanan anda berakhir!"
    accentColor="#fd7e14"
>
    <p style="margin:0 0 16px; font-size:15px; color:#343a40; line-height:1.6;">
        Yth. <strong>{{ $recipientRole === 'booker' ? $booking->user?->Name ?? 'Pengguna' : $booking->driver?->Name ?? 'Driver' }}</strong>,
    </p>

    <p style="margin:0 0 20px; font-size:15px; color:#343a40; line-height:1.6;">
        Kami informasikan bahwa perjalanan Anda telah <strong>berakhir</strong>.
    </p>

    {{-- Booking Detail Table --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:24px; border-radius:6px; overflow:hidden; border: 1px solid #dee2e6;">
        <tr style="background-color:#f8f9fa;">
            <td style="padding:12px 16px; font-size:13px; font-weight:700; color:#495057; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #dee2e6;" colspan="2">Detail Pemesanan</td>
        </tr>
        <tr>
            <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5; width:40%;">Nama Driver</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600; border-bottom:1px solid #f1f3f5;">{{ $booking->driver?->Name ?? '-' }}</td>
        </tr>
        <tr style="background-color:#fafafa;">
            <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5;">Nama Pemohon</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600; border-bottom:1px solid #f1f3f5;">{{ $booking->user?->Name ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5;">Tanggal & Waktu</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600; border-bottom:1px solid #f1f3f5;">
                {{ \Carbon\Carbon::parse($booking->scheduled_pickup_date)->translatedFormat('d M Y') }},
                pukul {{ \Carbon\Carbon::parse($booking->scheduled_pickup_time)->format('H.i') }}
                s/d {{ \Carbon\Carbon::parse($booking->scheduled_end_time)->format('H.i') }} WIB
            </td>
        </tr>
        <tr style="background-color:#fafafa;">
            <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5;">Tujuan</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600; border-bottom:1px solid #f1f3f5;">{{ $booking->destination ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:12px 16px; font-size:14px; color:#6c757d;">Keperluan</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600;">{{ $booking->purpose_of_trip ?? '-' }}</td>
        </tr>
    </table>

    <p style="margin:0 0 20px; font-size:14px; color:#343a40; line-height:1.6; text-align:center;">
        Terima kasih telah melakukan pemesanan ini! Mohon kesediaannya memberikan kesan dan pesan perjalanan Anda.
    </p>

    {{-- CTA Button --}}
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <a href="{{ route('home') }}"
                   style="display:inline-block; background-color:#6c757d; color:#ffffff; text-decoration:none; padding:12px 28px; border-radius:6px; font-size:14px; font-weight:700;">
                    Berikan Ulasan
                </a>
            </td>
        </tr>
    </table>
</x-emails.layouts.base>
