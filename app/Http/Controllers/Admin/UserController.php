<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        $roles = Role::get();
        return view('admin.user.index', compact('users', 'roles'));
    }


    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // Hanya Super Admin yang bisa tambah user
        $this->authorize('create-user'); // Pastikan permission 'create-user' ada

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|string|exists:roles,name', // Validasi: nama role harus ada
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department' => $request->department ?? 'Umum', // default
        ]);

        // Assign role berdasarkan nama
        $user->assignRole($request->role);

        return redirect()->route('admin.user.index')->with('toast_success', 'User berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Hanya Super Admin bisa ubah role
        $this->authorize('update-user'); // atau buat permission khusus seperti 'update-user-role'

        $request->validate([
            'role' => 'required|exists:roles,id',
        ]);

        // Sync role berdasarkan ID
        $user->syncRoles($request->role);

        return back()->with('toast_success', 'Role User Berhasil Diubah');
    }
}