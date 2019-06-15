<?php
use \Illuminate\Support\Facades\Route;

//\Motwreen\Translation\Translation::Routes();

Route::group(['namespace' => 'Motwreen\Translation\Http\Controllers', 'middleware' => ['web']], function() {
    Route::get('translation/ajax_validate/file_name', 'TranslationController@validateNewFileName')->name('translation.validate_file_name');
    Route::get('translation/ajax_read', 'TranslationController@readLangFileAjax')->name('translation.ajax_read_file');
    Route::post('translation/{locale}/', 'TranslationController@saveTranslations')->name('translation.save_translations');
    Route::resource('translation', 'TranslationController');
});
