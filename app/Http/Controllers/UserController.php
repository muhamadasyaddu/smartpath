<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Audit;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['wilayah']);

        if ($request->filled('peran')) {
            $query->peran($request->peran);
        }
        if ($request->filled('aktif') && $request->aktif === '1') {
            $query->aktif();
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('Admin.user.index', compact('users'));
    }

    public function create()
    {
        $wilayahList = \App\Models\Wilayah::aktif()->get();
        return view('admin.user.create', compact('wilayahList'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);
        $validated['email_terverifikasi'] = true;

        $user = User::create($validated);

        Audit::log(auth()->id(), 'buat_user', 'users', $user->id, null, ['nama_lengkap' => $user->nama_lengkap, 'email' => $user->email, 'peran' => $user->peran], "User baru: {$user->nama_lengkap}");

        return redirect()->route('Admin.user.index')->with('sukses', 'Pengguna berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $user->load(['wilayah', 'laporanDibuat' => fn($q) => $q->orderByDesc('created_at')->limit(5), 'notifikasi' => fn($q) => $q->orderByDesc('created_at')->limit(5)]);
        return view('admin.user.show', compact('user'));
    }

    public function edit(User $user)
    {
        $wilayahList = \App\Models\Wilayah::aktif()->get();
        return view('admin.user.edit', compact('user', 'wilayahList'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();
        if (isset($validated['kata_sandi'])) {
            $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);
        } else {
            unset($validated['kata_sandi']);
        }

        $dataLama = ['nama_lengkap' => $user->nama_lengkap, 'email' => $user->email, 'peran' => $user->peran];
        $user->update($validated);

        Audit::log(auth()->id(), 'ubah_user', 'users', $user->id, $dataLama, ['nama_lengkap' => $user->nama_lengkap, 'email' => $user->email, 'peran' => $user->peran], "User diubah: {$user->nama_lengkap}");

        return redirect()->route('Admin.user.index')->with('sukses', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('galat', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->route('Admin.user.index')->with('sukses', 'Pengguna berhasil dihapus.');
    }
}