<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class verses_translations extends Model
{
    protected $table = 'verse_translations';
    use HasFactory;

    public function Verses()
    {
        return $this->belongsTo(Verses::class);
    }

    public function translations()
    {
        return $this->belongsTo(translations::class);
    }

}
