<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class word_verses extends Model
{
    use HasFactory;

    public function Verses()
    {
        return $this->belongsTo(Verses::class);
    }

    public function word_translations()
    {
        return $this->hasMany(word_translations::class);
    }
}
