<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
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
}