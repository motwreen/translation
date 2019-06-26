<?php
namespace Motwreen\Translation;

use Illuminate\Support\Facades\Route;

class Translation
{
	/**
     * @param array $options
     * @return void
     */
    public static function routes(array $options = [])
    {
       $packageGroup = ['namespace' => 'Motwreen\Translation\Http\Controllers', 'middleware' => ['web']];
       $mergedGroups = array_merge($packageGroup,$group);
      
		Route::group($mergedGroups, function() {
		    Route::get('translation/ajax_validate/file_name', 'TranslationController@validateNewFileName')->name('translation.validate_file_name');
		    Route::get('translation/ajax_read', 'TranslationController@readLangFileAjax')->name('translation.ajax_read_file');
		    Route::post('translation/{locale}/', 'TranslationController@saveTranslations')->name('translation.save_translations');
		    Route::resource('translation', 'TranslationController');
		});
    }
}
