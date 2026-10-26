<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    public function index()
    {
        return view('admin.branches.index', [
            'branches' => Branch::query()->orderBy('kode')->get(),
            'branchLimit' => 5,
        ]);
    }

    public function store(Request $request)
    {
        $request->merge(['kode' => strtoupper(trim((string) $request->input('kode')))]);
        $validated = $request->validate([
            'kode' => ['required', 'string', 'size:3', 'regex:/^[A-Z0-9]{3}$/', 'unique:branches,kode'],
            'nama' => ['required', 'string', 'max:100', 'unique:branches,nama'],
        ]);

        if (Branch::count() >= 5) {
            return back()->withInput()->withErrors([
                'kode' => 'Maksimal lima cabang dapat dikelola.',
            ]);
        }

        Branch::create($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', Rule::unique('branches', 'nama')->ignore($branch->id)],
        ]);

        $branch->update($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Nama cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch)
    {
        $references = [
            ['kendaraans', 'kode_cabang'],
            ['stok_atks', 'kode_cabang'],
            ['pemakaian_atks', 'kode_cabang'],
            ['asets', 'kode_cabang'],
            ['permintaan_atks', 'kode_cabang'],
            ['atk_cabang_stoks', 'kode_cabang'],
        ];

        foreach ($references as [$table, $column]) {
            if (DB::table($table)->where($column, $branch->kode)->exists()) {
                return back()->withErrors([
                    'branch' => 'Cabang tidak dapat dihapus karena sudah digunakan oleh data aplikasi.',
                ]);
            }
        }

        if (DB::table('permintaan_atks')->where('unit_kerja', $branch->nama)->exists()) {
            return back()->withErrors([
                'branch' => 'Cabang tidak dapat dihapus karena sudah digunakan oleh permintaan ATK lama.',
            ]);
        }

        $branch->delete();

        return redirect()->route('admin.branches.index')->with('success', 'Cabang berhasil dihapus.');
    }
}
