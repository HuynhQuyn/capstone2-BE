<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $table = 'participants';

    protected $fillable = [
        'user_id',
        'cource_id',
        'is_register',
        'is_certificate',
        'grades',
        'date_range',
        'date_expired',
    ];
}
