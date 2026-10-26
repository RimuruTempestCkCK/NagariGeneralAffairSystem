<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'kode',
        'nama',
    ];

    public static function options(): array
    {
        return static::query()->orderBy('kode')->pluck('nama', 'kode')->all();
    }
}
