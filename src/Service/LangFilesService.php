<?php
namespace Motwreen\Translation\Services;

use Illuminate\Support\Arr;
use \Illuminate\Support\Facades\File;
Class LangFilesService
{
    public $default_lang      = 'en';
    protected $default_lang_path = 'resources/lang';

    public function __construct($lang_path = 'resources/lang')
    {
        $this->default_lang = config('app.locale');
        $this->default_lang_path = $lang_path;
    }

    public function createNewLangFilesFromDefault($new_lang,$default_lang=null)
    {
        $new_path       = base_path($this->default_lang_path.'/'.$new_lang);
        $default_path   = base_path($this->default_lang_path.'/'.($default_lang??$this->default_lang));

        $this->createDirectory($new_path);
        $this->copyFiles($default_path,$new_path);
    }

    public function createDirectory($path)
    {
        if(!File::exists($path))
            return File::makeDirectory($path);
        return true; //already exists
    }

    public function copyFiles($form,$to)
    {
        $files = File::files($form);
        foreach ($files as $file) {
            $new_file_path = $to.'/'.$file->getFilename() ;
            if(!File::exists($new_file_path)){
                $strings = include $file;
                $this->writeArrayToFile($strings,$new_file_path);
            }
        }
    }

    public function deleteDirectory($directory)
    {
        $path = base_path($this->default_lang_path.'/'.$directory);
        File::deleteDirectory($path);
    }

    public function updateLangFile($lang,$file,$data)
    {
        $path = base_path($this->default_lang_path.'/'.$lang.'/'.$file);
        $this->writeArrayToFile($data,$path);
    }

    public function appendKeysToFile($lang,$file,$new_data)
    {
        $path = base_path($this->default_lang_path.'/'.$lang.'/'.$file);
        $currentData = (@include $path)?include $path:[];
        $data = array_merge_recursively($currentData,$new_data);
        $this->writeArrayToFile($data,$path);
    }

    protected function writeArrayToFile($array,$new_file_path)
    {
        $string = "<?php".PHP_EOL;
        $string .= "return";
        $string .= varexport($array,true);
        $string .= ";";
        file_put_contents($new_file_path,$string);
    }

    public function readArrayFromFile($lang,$file)
    {
        $otherArray =  Arr::dot(include base_path($this->default_lang_path.'/'.$lang.'/'.$file));
        $defaultArray = Arr::dot(include base_path($this->default_lang_path.'/'.$this->default_lang.'/'.$file));

//        $keysArray = array_keys($defaultArray) + array_keys($defaultArray);
        $keysArray = array_unique(array_merge(array_keys($defaultArray),array_keys($otherArray)));

        $result=[];
        foreach ($keysArray as $key){
            if($lang !=$this->default_lang)
                $result[$key]['default']=$defaultArray[$key]??"";
            $result[$key]['other']=$otherArray[$key]??"";
        }
        return $result;
    }
}
