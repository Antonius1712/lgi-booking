<x-emails.layouts.base
    title="{{ $recipientRole === 'driver' ? 'Anda Memiliki Perpanjangan Waktu Perjalanan' : 'Permintaan Perpanjangan Waktu Anda Disetujui' }}"
    accentColor="#fd7e14"
>
    <p style="margin:0 0 16px; font-size:15px; color:#343a40; line-height:1.6;">
        Yth. <strong>{{ $recipientRole === 'booker' ? $booking->user?->Name ?? 'Pengguna' : $booking->driver?->Name ?? 'Driver' }}</strong>,
    </p>

    @if ($recipientRole === 'booker')
        <p style="margin:0 0 20px; font-size:15px; color:#343a40; line-height:1.6;">
            Permintaan <strong>perpanjangan waktu</strong> perjalanan Anda telah <strong style="color:#fd7e14">disetujui</strong> oleh admin.
            Waktu selesai perjalanan telah diperbarui.
        </p>
    @else
        <p style="margin:0 0 20px; font-size:15px; color:#343a40; line-height:1.6;">
            Anda memiliki <strong>perpanjangan waktu</strong> untuk perjalanan ini.
            Harap perhatikan waktu selesai yang telah diperbarui di bawah.
        </p>
    @endif

    {{-- Booking Detail Table --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:24px; border-radius:6px; overflow:hidden; border: 1px solid #dee2e6;">
        <tr style="background-color:#f8f9fa;">
            <td style="padding:12px 16px; font-size:13px; font-weight:700; color:#495057; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid #dee2e6;" colspan="2">Detail Pemesanan (Diperbarui)</td>
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

    {{-- Highlighted new end time --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff3e0; border:1px solid #fd7e14; border-radius:6px; margin-bottom:24px;">
        <tr>
            <td style="padding:14px 16px; text-align:center;">
                <p style="margin:0; font-size:13px; color:#a04a00; font-weight:700;">&#10003; Waktu Selesai Baru</p>
                <p style="margin:4px 0 0; font-size:20px; font-weight:700; color:#a04a00;">
                    {{ \Carbon\Carbon::parse($booking->scheduled_end_time)->format('H:i') }} WIB
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0; font-size:14px; color:#6c757d; line-height:1.6; text-align:center;">
        Semoga perjalanan Anda menyenangkan dan sampai tujuan dengan selamat.
    </p>
</x-emails.layouts.base>
