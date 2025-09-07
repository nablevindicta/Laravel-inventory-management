@extends('layouts.master', ['title' => 'User'])

@section('content')
    <x-container>
        <div class="col-12">

            <!-- Tombol Tambah User -->
            <div class="mb-4">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="fas fa-plus"></i> Tambah User Admin
                </button>
            </div>

            <!-- Modal: Tambah User Baru -->
            <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createUserModalLabel">Tambah User Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.user.store') }}" method="POST" id="createUserForm">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="name" class="form-control" placeholder="Masukkan nama" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <input type="text" class="form-control" value="Admin" disabled>
                                    <input type="hidden" name="role" value="Admin">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" form="createUserForm" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ INI BAGIAN YANG DIUBAH — TABEL UTAMA USER --}}
            <div class="card shadow-sm mb-4">
                <!-- Header -->
                <div class="card-header bg-white border-bottom d-flex align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        <strong>DAFTAR USER</strong>
                    </div>
                    <!-- Tidak ada tombol tambah di header, karena sudah ada di atas -->
                </div>

                <!-- Body: Tabel -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-3">
                            <thead class="text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($users as $i => $user)
                                    <tr>
                                        <td>{{ $i + $users->firstItem() }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach ($user->roles as $role)
                                                <span class="badge bg-primary">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <!-- Tombol Edit -->
                                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- Modal Edit -->
                                            <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Ubah Role: {{ $user->name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="{{ route('admin.user.update', $user->id) }}" method="POST" id="editForm{{ $user->id }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="mb-3">
                                                                    <label class="form-label">Nama</label>
                                                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Role</label>
                                                                    <select name="role" class="form-control" required>
                                                                        <option value="">Pilih Role</option>
                                                                        @foreach ($roles as $role)
                                                                            <option value="{{ $role->id }}" {{ $user->roles->contains($role) ? 'selected' : '' }}>
                                                                                {{ $role->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" form="editForm{{ $user->id }}" class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </x-container>
@endsection