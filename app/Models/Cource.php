<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cource extends Model
{
    use HasFactory;

    protected $table = 'cources';

    protected $fillable = [
        'cource_name',
        'cource_image',
        'cource_type',
        'cource_introduce',
        'cource_description',
        'is_block',
    ];
}
