<?php
namespace Motwreen\Translation\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    public function locale()
    {
        return $this->belongsTo(Locale::class,'locale_id');
    }
}
