<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'departement_id',
        'name',
        'phone',
        'address',
    ];
    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }
}
