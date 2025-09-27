<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Traits\HasImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    use HasImage;

    public function index()
    {
        $user = Auth::user();
        return view('admin.setting.index', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $image = $this->uploadImage($request, 'public/avatars/', 'avatar');

        // Validasi: nama wajib, password konfirmasi wajib dan harus benar
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required', // Hanya untuk konfirmasi
        ], [
            'password.required' => 'Password diperlukan untuk konfirmasi perubahan.'
        ]);

        // Cek apakah password yang dimasukkan benar
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Password Anda salah. Perubahan tidak dapat disimpan.'
            ])->withInput($request->only('name'));
        }

        // Update hanya nama (dan avatar), password TETAP SAMA
        $user->update([
            'name' => $request->name,
            // 'password' => $user->password, // tidak diubah
        ]);

        // Simpan avatar jika diupload
        if ($request->file('avatar')) {
            $this->updateImage('public/avatars/', 'avatar', $user, $image->hashName());
        }

        return back()->with('toast_success', 'Akun berhasil diperbarui.');
    }
}
