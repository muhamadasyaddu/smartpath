<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Notifikasi SmartPath</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f6f9fc; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0;">
        <h2 style="color: #065f46; margin-top: 0;">SmartPath Notification</h2>
        <p style="font-size: 15px; color: #334155;">Halo,</p>
        <p style="font-size: 15px; color: #334155; line-height: 1.5;">{{ $pesanNotif }}</p>
        
        @if($judulLaporan)
            <div style="background: #f1f5f9; padding: 12px 15px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0; font-size: 13px; color: #64748b;">Terkait Laporan:</p>
                <p style="margin: 4px 0 0 0; font-size: 14px; font-weight: bold; color: #0f172a;">{{ $judulLaporan }}</p>
            </div>
        @endif

        <p style="font-size: 13px; color: #94a3b8; margin-top: 30px; border-top: 1px solid #e2e8f0; pt-top: 15px;">
            Pesan otomatis dikirim oleh sistem SmartPath. Mohon tidak membalas email ini.
        </p>
    </div>
</body>
</html>