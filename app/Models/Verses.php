<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verses extends Model
{
    use HasFactory;

    public function verse_translations()
    {
        return $this->hasMany(verses_translations::class);
    }

    public function word_verses()
    {
        return $this->hasMany(word_verses::class);
    }

    public function chapters()
    {
        return $this->belongsTo(Chapters::class);
    }

}
