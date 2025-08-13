<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoParagraph extends Model
{
    use HasFactory;

    protected $fillable = ['paragraph', 'embedding'];
    protected $casts = [
        'embedding' => 'array',
    ];
}
