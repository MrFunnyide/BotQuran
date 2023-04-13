<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class word_translations extends Model
{
    use HasFactory;

    public function word_verses()
    {
        return $this->belongsTo(word_verses::class);
    }
}
