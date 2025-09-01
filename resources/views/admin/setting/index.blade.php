@extends('layouts.master', ['title' => 'Akun'])

@section('content')
    <x-container>
        <div class="col-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <x-card title="PROFILE" class="card-body">
                <form action="{{ route('admin.setting.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <x-input title="Nama" name="name" type="text" :value="$user->name" />

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email"
                               class="form-control"
                               value="{{ $user->email }}"
                               readonly>
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <small class="text-muted">Email tidak dapat diubah.</small>
                    </div>

                    <x-input 
                        title="Konfirmasi Password" 
                        name="password" 
                        type="password" 
                        placeholder="Masukkan password Anda untuk konfirmasi" 
                        value="" 
                    />

                    <x-button-save title="Update" icon="save" class="btn btn-primary" />
                </form>
            </x-card>
        </div>
    </x-container>
@endsection