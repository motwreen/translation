<?php
namespace Motwreen\Translation\Facades;

use Motwreen\Translation\Translation as TranslationClass;
use Illuminate\Support\Facades\Facade;
/**
 * @method static routes(array $options)
 * @see \Motwreen\Translation\Translation;
 */

class Translation extends Facade
{
	protected static function getFacadeAccessor() { return TranslationClass::class; }
}