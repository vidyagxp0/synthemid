<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentAnnexure extends Model
{
    use HasFactory;

    public function childs()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
