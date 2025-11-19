<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Secara default Laravel sudah pakai tabel 'employees',
    // jadi tidak wajib mendefinisikan $table kecuali nama tabel beda

    /**
     * Field yang bisa diisi mass assignment (create/update).
     */
    protected $fillable = [
        'name',
        'email',
        'position',
        'salary',
        'status',
        'hired_at',
    ];

    /**
     * Casting field ke tipe tertentu.
     */
    protected $casts = [
        'hired_at' => 'date',       // jadi instance Carbon (Y-m-d)
        'salary'   => 'integer',
    ];
}
