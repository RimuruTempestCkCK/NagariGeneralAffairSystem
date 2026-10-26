@extends('layout.app')

@section('title', 'Pengelolaan Cabang')
@section('breadcrumbs', 'Pengaturan | Pengelolaan Cabang')

@section('content')
<style>
    .branch-create-form {
        display: grid;
        grid-template-columns: minmax(150px, 0.7fr) minmax(240px, 1.5fr) auto;
        gap: 16px;
        align-items: start;
    }
    .branch-create-form .form-group {
        min-width: 0;
    }
    .branch-create-form label {
        display: block;
        min-height: 20px;
        margin-bottom: 6px;
    }
    .branch-create-form .input {
        width: 100%;
        height: 42px;
        box-sizing: border-box;
    }
    .branch-create-form small {
        display: block;
        min-height: 18px;
        margin-top: 5px;
        color: var(--t-muted);
        line-height: 1.4;
    }
    .branch-create-form button {
        min-height: 42px;
        margin-top: 26px;
    }
    .branch-create-form button:disabled {
        cursor: not-allowed;
        opacity: 0.55;
    }
    @media (max-width: 720px) {
        .branch-create-form {
            grid-template-columns: 1fr;
        }
        .branch-create-form button {
            margin-top: 0;
        }
        .branch-create-form button {
            width: 100%;
        }
    }
</style>

<section class="hero">
    <div class="hero-text">
        <span class="eyebrow">Pengaturan</span>
        <h1 class="hero-title">Pengelolaan Cabang</h1>
        <p class="hero-sub">Daftar cabang ini digunakan sebagai pilihan di formulir, filter, dan laporan aplikasi. Maksimal {{ $branchLimit }} cabang.</p>
    </div>
</section>

@if(session('success'))
    <div class="card" style="padding: 14px 18px; margin-bottom: 16px; color: var(--success);">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="card" style="padding: 14px 18px; margin-bottom: 16px; color: var(--danger, #b91c1c);">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<section class="card col-12" style="padding: 20px; margin-bottom: 20px;">
    <h2 class="card-title" style="margin-bottom: 14px;">Tambah Cabang</h2>
    <form method="POST" action="{{ route('admin.branches.store') }}" class="branch-create-form">
        @csrf
        <div class="form-group" style="margin: 0;">
            <label for="kode">Kode Cabang</label>
            <input class="input" id="kode" name="kode" value="{{ old('kode') }}" maxlength="3" pattern="[A-Za-z0-9]{3}" placeholder="006" required>
            <small>3 huruf/angka. Kode tidak dapat diubah setelah disimpan.</small>
        </div>
        <div class="form-group" style="margin: 0;">
            <label for="nama">Nama Cabang</label>
            <input class="input" id="nama" name="nama" value="{{ old('nama') }}" maxlength="100" placeholder="Nama cabang" required>
            <small aria-hidden="true">&nbsp;</small>
        </div>
        @if($branches->count() < $branchLimit)
            <button class="btn btn--primary" type="submit">Tambah Cabang</button>
        @else
            <button class="btn btn--primary" type="submit" disabled title="Hapus cabang yang belum digunakan terlebih dahulu">Tambah Cabang</button>
        @endif
    </form>
    @if($branches->count() >= $branchLimit)
        <p style="margin: 14px 0 0; color: var(--t-muted);">Batas maksimal {{ $branchLimit }} cabang sudah tercapai. Untuk menambah cabang, hapus cabang yang belum digunakan terlebih dahulu.</p>
    @endif
</section>

<section class="card col-12">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Master Data</span>
            <h2 class="card-title">Daftar Cabang ({{ $branches->count() }}/{{ $branchLimit }})</h2>
        </div>
    </div>
    <div class="table-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Cabang</th>
                    <th>Terakhir Diperbarui</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($branches as $branch)
                <tr>
                    <td><strong>{{ $branch->kode }}</strong></td>
                    <td>
                        <form id="branch-update-{{ $branch->id }}" method="POST" action="{{ route('admin.branches.update', $branch) }}">
                            @csrf
                            @method('PUT')
                            <input class="input" name="nama" value="{{ $branch->nama }}" maxlength="100" required aria-label="Nama {{ $branch->kode }}">
                        </form>
                    </td>
                    <td>{{ $branch->updated_at->format('d M Y H:i') }}</td>
                    <td style="text-align: right; white-space: nowrap;">
                        <button class="btn btn--primary btn--xs" type="submit" form="branch-update-{{ $branch->id }}">Simpan Nama</button>
                        <form method="POST" action="{{ route('admin.branches.destroy', $branch) }}" style="display: inline;" onsubmit="return confirm('Hapus cabang {{ $branch->kode }}? Cabang yang sudah dipakai pada data tidak dapat dihapus.')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn--ghost btn--xs" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align: center; padding: 30px; color: var(--t-muted);">Belum ada cabang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
