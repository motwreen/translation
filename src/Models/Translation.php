<?php
namespace Motwreen\Translation\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use UsesTenantConnection;

    public function locale()
    {
        return $this->belongsTo(Locale::class,'locale_id');
    }
}
