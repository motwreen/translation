<?php
namespace Motwreen\Translation\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    use UsesTenantConnection;
    public function getDefaultAttribute()
    {
        return $this->iso == config('app.locale');
    }
}
