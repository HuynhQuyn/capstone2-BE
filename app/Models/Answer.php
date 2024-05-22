<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    protected $table = 'answers';

    protected $fillable = [
        'class_id',
        'excercise_id',
        'user_id',
        'answer_content',
        'status',
    ];
}
