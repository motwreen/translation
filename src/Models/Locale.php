<?php
namespace Motwreen\Translation\Models;

use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{

    public function getDefaultAttribute()
    {
        return $this->iso == config('app.locale');
    }
}
