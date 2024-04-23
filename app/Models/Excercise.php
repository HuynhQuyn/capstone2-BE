<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Excercise extends Model
{
    use HasFactory;

    protected $table = 'excercises';

    protected $fillable = [
        'excercise_name',
        'excercise_content',
        'excercise_type',
    ];
}
