<?php
namespace Motwreen\Translation\Facades;

use Motwreen\Translation\Translation;

use Illuminate\Support\Facades\Facade;

class Translation extends Facade
{
	protected static function getFacadeAccessor() { return Translation::class; }
}