<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassDetail extends Model
{
    use HasFactory;
    protected $table = 'class_details';

    protected $fillable = [
        'title',
        'date',
        'id_class',
        'link',
        'id_excercise',
    ];
}
