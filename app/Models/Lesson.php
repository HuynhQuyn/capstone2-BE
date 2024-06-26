<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $table = 'lessons';

    protected $fillable = [
        'lesson_name',
        'lesson_video',
        'time',
        'id_excercise',
        'id_cource',
        'id_chapter',
        'position',
    ];
}
