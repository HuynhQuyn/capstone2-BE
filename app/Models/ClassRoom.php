<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;
    protected $table = 'class_rooms';

    protected $fillable = [
        'duration',
        'class_name',
        'time',
        'weekday_selection',
        'room_id',
        'id_cource',
        'teacher',
        'students',
    ];
}
