<?php

namespace Motwreen\Translation\Traits;

use function foo\func;
use Motwreen\Translation\Models\Locale;
use Motwreen\Translation\Models\Translation;

trait TranslatableTrait
{
    public $locale;
    public $translationRow = [];

    public static $toTranslate = [];
    protected static function boot()
    {
        parent::boot();
        static::retrieved(function ($model)
        {
            $locale = (new self)->defaultLocale();
            $model->translationRow =  Translation::where("model", get_class($model))->where("model_id", $model->id)->get();
            $model->getTranslatedAttributes();
        });

        static::saving(function ($model){
            $translatable = (new self)->getTranslatable();
            foreach ($translatable as $attribute){
                self::$toTranslate[$attribute] = $model->attributes[$attribute];
                unset($model->attributes[$attribute]);
            }
        });

        static::saved(function ($model){
            (new self)->translateAttributes(self::$toTranslate,$model->id );
            foreach (self::$toTranslate as $attribute => $value)
                $model->attributes[$attribute] = $value[app()->getLocale()];

        });

        static::deleted(function ($model) {
            $model->translations()->delete();
        });
    }

    public function translations(){
        return $this->hasMany( Translation::class,'model_id')->where('model',get_class($this));
    }

    public function getAllTranslations()
    {
        $locales = $this->locales();
        foreach ($locales as $locale)
            $trans[$locale->iso]=$this->getTranslatedAttributes($locale->id);
        return $trans;
    }

    public function getTranslatedAttributes($locale=null)
    {
        $res = [];
        $locale??$this->locales()->where('iso',app()->getLocale())->first()->id;
        foreach ($this->translatable as $key) {
            $this->{$key} = $this->getTranslation($key,$locale);
        }
        return $res;
    }

    protected function defaultLocale()
    {
        return (new self)->locales()->where('iso', app()->getLocale())->first()->id;
    }

    public function __get($key)
    {
        if (isset($this->translatable) && in_array($key, $this->translatable) && $key != 'translationRow') {
            //translate and return
            return $this->getTranslation($key);
        }
        return parent::__get($key);
        //don't translate, call parent

    }

    public function getTranslation($key, $locale = NULL)
    {
        if (!$locale) {
            $locale = $this->defaultLocale();
        }
        if (count($this->translationRow) == 0) {
            return "";
        }
        if(!$this->translationRow->where('attribute',$key)->first())
            return "";
        if(!$this->translationRow->where('locale_id',$locale)->first()) {
            if($this->showAlternateLocaleIfRequestedNull)
                return $this->translationRow->where('attribute',$key)->first()->value;
            return "";
        }
        return $this->translationRow->where('attribute',$key)->where('locale_id',$locale)->first()->value;
    }

    protected function setTranslation($key, $value, $locale = NULL,$model_id)
    {
        if(!in_array($key,$this->translatable))
            return false;
        if (!$locale) {
            $locale = $this->defaultLocale();
        }

        $translation = Translation::where("model", get_class($this))->where("model_id", $model_id)->where("attribute", $key)->where("locale_id", $locale)->first();

        if (!$translation) {
            $translation = new Translation;
            $translation->model = get_class($this);
            $translation->model_id = $model_id;
            $translation->attribute = $key;
            $translation->locale_id = $locale;
            $translation->value = $value;
        } else {
            $translation->value = $value;
        }

        return $translation->save();
    }

//    public function toJson($locale = NULL)
//    {
//        if (!$locale) {
//            $locale = $this->defaultLocale();
//        }
//        $array = $this->toArray();
//        if (isset($this->translatable)) {
//            foreach ($this->translatable as $value) {
//                $array[$value] = $this->getTranslation($value, $locale);
//            }
//        }
//        return json_encode($array);
//    }

    protected function translateAttributes(array $values,$model_id)
    {
        $locals = (new self)->locales()->pluck('iso','id')->toArray();
        foreach ($values as $key => $tanslations) {
            foreach ($tanslations as $iso => $value) {
                if (in_array($iso, $locals)) {
                    $this->setTranslation($key, $value, array_flip($locals)[$iso],$model_id);
                }
            }
        }
    }

    public function deleteTranslations($locale = null)
    {
        $model_id = $this->id;
        $translations = Translation::where("model", get_class($this))->where("model_id", $model_id);
        if ($locale != null) {
            $translations->where("locale_id", $locale);
        }
        $translations->delete();
    }

    public static function makeSlug($string, $locale = null)
    {
        if (!$locale) {
            $locale = (new self)->defaultLocale();
        }
        $slug = make_slug($string);
        $slug = preg_replace('/-[0-9]*$/', '', $slug);
        $locale = (new self)->locales()->where('code', $locale)->first();
        $record = Translation::where('model', get_class(new self))
            ->where('locale_id', $locale->id)
            ->where('attribute', 'slug')
            ->where('value', 'REGEXP', $slug . '?-?([0-9]*$)')
            ->latest('id')->first();
        if ($record)
            $slug = increment_string($record->value);

        return $slug;
    }

    public function getCasts()
    {
        return $this->casts;
    }

    protected function getTranslatable()
    {
        return $this->translatable;
    }


    protected function locales()
    {
        return Locale::all();
    }

}
