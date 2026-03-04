<x-emails.layouts.base
    title="{{ $recipientRole === 'admin' ? 'Permintaan Perpanjangan Waktu Perjalanan' : 'Permintaan Perpanjangan Waktu Anda Terkirim' }}"
    accentColor="#fd7e14"
>
    <p style="margin:0 0 16px; font-size:15px; color:#343a40; line-height:1.6;">
        Yth. <strong>{{ $recipientRole === 'admin' ? 'Admin' : $booking->user?->Name ?? 'Pengguna' }}</strong>,
    </p>

    @if ($recipientRole === 'booker')
        <p style="margin:0 0 20px; font-size:15px; color:#343a40; line-height:1.6;">
            Permintaan <strong>perpanjangan waktu</strong> perjalanan Anda telah berhasil dikirim dan sedang menunggu persetujuan admin.
        </p>
    @else
        <p style="margin:0 0 20px; font-size:15px; color:#343a40; line-height:1.6;">
            Terdapat permintaan <strong>perpanjangan waktu perjalanan</strong> baru yang memerlukan persetujuan Anda.
        </p>
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
            <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5;">Nama Driver</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600; border-bottom:1px solid #f1f3f5;">{{ $booking->driver?->Name ?? '-' }}</td>
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
            <td style="padding:12px 16px; font-size:14px; color:#6c757d; border-bottom:1px solid #f1f3f5;">Durasi Perpanjangan</td>
            <td style="padding:12px 16px; font-size:14px; font-weight:700; border-bottom:1px solid #f1f3f5;" style="color:#fd7e14;">
                @php
                    $mins = $booking->extension_duration ?? 0;
                    $label = $mins >= 60
                        ? floor($mins / 60).'j'.($mins % 60 > 0 ? ' '.($mins % 60).'m' : '')
                        : $mins.' menit';
                @endphp
                + {{ $label }}
            </td>
        </tr>
        <tr style="background-color:#fafafa;">
            <td style="padding:12px 16px; font-size:14px; color:#6c757d;">Waktu Selesai Saat Ini</td>
            <td style="padding:12px 16px; font-size:14px; color:#212529; font-weight:600;">
                {{ \Carbon\Carbon::parse($booking->scheduled_end_time)->format('H.i') }} WIB
            </td>
        </tr>
    </table>

    @if ($recipientRole === 'admin')
        {{-- Info Box for Admin --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff3cd; border:1px solid #ffc107; border-radius:6px; margin-bottom:24px;">
            <tr>
                <td style="padding:14px 16px;">
                    <p style="margin:0; font-size:13px; color:#856404; font-weight:700;">&#9432; Tindakan Diperlukan</p>
                    <p style="margin:6px 0 0; font-size:13px; color:#856404; line-height:1.6;">
                        Silakan buka panel admin untuk menyetujui atau menolak permintaan perpanjangan waktu ini.
                    </p>
                </td>
            </tr>
        </table>

        {{-- CTA Button --}}
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center">
                    <a href="{{ route('admin.driver-bookings.show', $booking) }}"
                       style="display:inline-block; background-color:#fd7e14; color:#ffffff; text-decoration:none; padding:12px 28px; border-radius:6px; font-size:14px; font-weight:700;">
                        Buka Panel Admin
                    </a>
                </td>
            </tr>
        </table>
    @else
        <p style="margin:0; font-size:14px; color:#6c757d; line-height:1.6; text-align:center;">
            Anda akan mendapatkan notifikasi setelah admin menyetujui atau menolak permintaan Anda.
        </p>
    @endif
</x-emails.layouts.base>
