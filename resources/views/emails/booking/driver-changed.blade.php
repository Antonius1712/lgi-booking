<x-emails.layouts.base
    title="Perubahan Driver Pemesanan!"
    accentColor="#fd7e14"
>
    <p style="margin:0 0 16px; font-size:15px; color:#343a40; line-height:1.6;">
        @if($recipientRole === 'booker')
            Yth. <strong>{{ $booking->user?->Name ?? 'Pengguna' }}</strong>,
        @elseif($recipientRole === 'old_driver')
            Yth. <strong>{{ $oldDriver->Name }}</strong>,
        @else
            Yth. <strong>{{ $booking->driver?->Name ?? 'Driver' }}</strong>,
        @endif
    </p>

    @if($recipientRole === 'booker')
        <p style="margin:0 0 20px; font-size:15px; color:#343a40; line-height:1.6;">
            Kami menginformasikan bahwa driver untuk pemesanan Anda telah <strong>diganti</strong> oleh admin. Berikut detail perubahan:
        </p>
    @elseif($recipientRole === 'old_driver')
        <p style="margin:0 0 20px; font-size:15px; color:#343a40; line-height:1.6;">
            Penugasan perjalanan berikut telah <strong>dialihkan</strong> kepada driver lain. Anda tidak perlu melakukan tindakan apapun.
        </p>
    @else
        <p style="margin:0 0 20px; font-size:15px; color:#343a40; line-height:1.6;">
            Anda telah <strong>ditugaskan</strong> untuk perjalanan berikut. Harap persiapkan diri sesuai jadwal.
        </p>
    @endif

    {{-- Driver Change Info (booker only) --}}
    @if($recipientRole === 'booker')
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:24px; border-radius:6px; overflow:hidden; border:1px solid #dee2e6;">
            <tr style="background-color:#f8f9fa;">
                <td style="padding:12px 16px; font-size:13px; font-weight:700; color:#495057; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #dee2e6;" colspan="2">Perubahan Driver</td>
            </tr>
            <tr>
                <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5; width:40%;">Driver Sebelumnya</td>
                <td style="padding:12px 16px; font-size:14px; color:#dc3545; font-weight:600; border-bottom:1px solid #f1f3f5;">{{ $oldDriver->Name }}</td>
            </tr>
            <tr style="background-color:#fafafa;">
                <td style="padding:12px 16px; font-size:14px; color:#6c757d;">Driver Baru</td>
                <td style="padding:12px 16px; font-size:14px; color:#28a745; font-weight:600;">{{ $booking->driver?->Name ?? '-' }}</td>
            </tr>
        </table>
    @endif

    {{-- Booking Detail Table --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:24px; border-radius:6px; overflow:hidden; border: 1px solid #dee2e6;">
        <tr style="background-color:#f8f9fa;">
            <td style="padding:12px 16px; font-size:13px; font-weight:700; color:#495057; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #dee2e6;" colspan="2">Detail Pemesanan</td>
        </tr>
        <tr>
            <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5; width:40%;">Nama Pemohon</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600; border-bottom:1px solid #f1f3f5;">{{ $booking->user?->Name ?? '-' }}</td>
        </tr>
        <tr style="background-color:#fafafa;">
            <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5;">Tanggal & Waktu</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600; border-bottom:1px solid #f1f3f5;">
                {{ \Carbon\Carbon::parse($booking->scheduled_pickup_date)->translatedFormat('d M Y') }},
                pukul {{ \Carbon\Carbon::parse($booking->scheduled_pickup_time)->format('H.i') }}
                s/d {{ \Carbon\Carbon::parse($booking->scheduled_end_time)->format('H.i') }} WIB
            </td>
        </tr>
        <tr>
            <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5;">Tujuan</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600; border-bottom:1px solid #f1f3f5;">{{ $booking->destination ?? '-' }}</td>
        </tr>
        <tr style="background-color:#fafafa;">
            <td style="padding:12px 16px; font-size:14px; color:#6c757d;">Keperluan</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600;">{{ $booking->purpose_of_trip ?? '-' }}</td>
        </tr>
    </table>

    {{-- Info Box --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff3cd; border:1px solid #ffc107; border-radius:6px; margin-bottom:24px;">
        <tr>
            <td style="padding:14px 16px;">
                <p style="margin:0; font-size:13px; color:#856404; font-weight:700;">&#9432; Informasi Penting</p>
                @if($recipientRole === 'booker')
                    <p style="margin:6px 0 0; font-size:13px; color:#856404; line-height:1.6;">
                        Perjalanan Anda tetap berjalan sesuai jadwal. Hubungi admin jika ada pertanyaan.
                    </p>
                @elseif($recipientRole === 'old_driver')
                    <p style="margin:6px 0 0; font-size:13px; color:#856404; line-height:1.6;">
                        Anda tidak perlu hadir untuk perjalanan ini. Slot waktu Anda kini tersedia kembali.
                    </p>
                @else
                    <p style="margin:6px 0 0; font-size:13px; color:#856404; line-height:1.6;">
                        Harap pastikan Anda siap sesuai jadwal yang tercantum di atas.
                    </p>
                @endif
            </td>
        </tr>
    </table>

    {{-- CTA Button (booker and new driver only) --}}
    @if($recipientRole !== 'old_driver')
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
    @endif
</x-emails.layouts.base>
