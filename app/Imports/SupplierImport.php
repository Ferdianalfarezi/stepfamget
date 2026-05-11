<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithUpserts;

class SupplierImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, WithUpserts
{
    /**
     * Kolom unik untuk upsert — kalau nama sudah ada, update alamatnya.
     * Kalau tidak mau upsert (selalu insert), hapus implements WithUpserts + method uniqueBy().
     */
    public function uniqueBy(): string
    {
        return 'nama';
    }

    public function model(array $row): Supplier
    {
        return new Supplier([
            'nama'   => trim($row['nama']   ?? $row['nama_supplier'] ?? ''),
            'alamat' => trim($row['alamat'] ?? $row['address']       ?? ''),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama.required' => 'Kolom "nama" wajib diisi.',
        ];
    }
}