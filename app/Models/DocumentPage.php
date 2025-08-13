<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentPage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function document()
    {
        return $this->belongsTo(Documents::class);
    }
}
