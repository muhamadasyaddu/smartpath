<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Laporan;
use App\Mail\NotifikasiLaporanMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Notifikasi umum untuk warga/dinas.
     */
    public function index(Request $request)
    {
        $notifikasi = Notifikasi::query()
            ->where('penerima_id', auth()->id())
            ->with('laporan')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $belumDibaca = Notifikasi::query()
            ->where('penerima_id', auth()->id())
            ->belumDibaca()
            ->count();

        return view(
            'notifikasi.index',
            compact(
                'notifikasi',
                'belumDibaca'
            )
        );
    }

    /**
     * =====================================================
     * ADMINISTRATOR
     * =====================================================
     */

    public function adminIndex(Request $request)
    {
        abort_unless(
            auth()->user()->isAdmin(),
            403
        );

        $notifikasi = Notifikasi::query()
            ->where(
                'penerima_id',
                auth()->id()
            )
            ->with('laporan')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $belumDibaca = Notifikasi::query()
            ->where(
                'penerima_id',
                auth()->id()
            )
            ->belumDibaca()
            ->count();

        return view(
            'Admin.notifikasi.index',
            compact(
                'notifikasi',
                'belumDibaca'
            )
        );
    }

    public function adminMarkAsRead(
        Notifikasi $notifikasi
    ) {
        abort_unless(
            auth()->user()->isAdmin(),
            403
        );

        abort_unless(
            $notifikasi->penerima_id === auth()->id(),
            403
        );

        $notifikasi->markAsRead();

        if ($notifikasi->tautan) {
            return redirect(
                $notifikasi->tautan
            );
        }

        return back();
    }

    public function adminMarkAllAsRead()
    {
        abort_unless(
            auth()->user()->isAdmin(),
            403
        );

        Notifikasi::query()
            ->where(
                'penerima_id',
                auth()->id()
            )
            ->belumDibaca()
            ->update([
                'sudah_dibaca' => true,
                'dibaca_pada' => now(),
            ]);

        return back()->with(
            'sukses',
            'Semua notifikasi telah ditandai sebagai dibaca.'
        );
    }

    /**
     * =====================================================
     * NOTIFIKASI UMUM
     * =====================================================
     */

    public function markAsRead(
        Notifikasi $notifikasi
    ) {
        abort_unless(
            $notifikasi->penerima_id === auth()->id(),
            403
        );

        $notifikasi->markAsRead();

        if ($notifikasi->tautan) {
            return redirect(
                $notifikasi->tautan
            );
        }

        return back();
    }

    public function markAllAsRead()
    {
        Notifikasi::query()
            ->where(
                'penerima_id',
                auth()->id()
            )
            ->belumDibaca()
            ->update([
                'sudah_dibaca' => true,
                'dibaca_pada' => now(),
            ]);

        return back()->with(
            'sukses',
            'Semua notifikasi telah ditandai sebagai dibaca.'
        );
    }

    public function unreadCount()
    {
        return response()->json([
            'count' =>
                auth()
                    ->user()
                    ->notifikasi_belum_dibaca_count,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);
        $laporan->status = $request->status;
        $laporan->save();

        $userPemilikLaporan = $laporan->user;

        if ($userPemilikLaporan) {
            $notif = Notifikasi::create([
                'penerima_id' => $userPemilikLaporan->id,
                'laporan_id'  => $laporan->id,
                'judul'       => 'Status Laporan Berubah',
                'pesan'       => 'Laporan Anda sekarang berstatus: ' . $laporan->status,
                'tautan'      => route('laporan.show', $laporan->id),
            ]);

            if ($userPemilikLaporan->email) {
                try {
                    Mail::to($userPemilikLaporan->email)->send(
                        new NotifikasiLaporanMail($notif->pesan, $laporan->judul)
                    );
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim email notifikasi laporan', [
                        'laporan_id' => $laporan->id,
                        'user_id' => $userPemilikLaporan->id,
                        'email' => $userPemilikLaporan->email,
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        }

        return redirect()->back()->with('sukses', 'Status laporan berhasil diperbarui.');
    }
}