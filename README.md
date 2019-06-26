# Laravel Translation
A simple GUI for Laravel Translation.

This package will allows you to take control of `lang` files in Laravel infrastructre .
it will also allows you to translate user inputs (models or database columns data)

## Installation
So simple just run `composer require motwreen/translation` in your application terminal.

## Publish
If you'd like to edit package views run `php artisan vendor:publish --tag=motwreen-translation` in your terminal and you'll find it in this path : `resources/views/vendor/translation`.

## Routes
you can include translations routes in your application like this in `web.php` file.
```
$options = ['prefix'=>'admin/translation/','middleware'=>['web','auth:admin']];
Translations::routes($options);
```


## Migrate
To migrate database tables run `php artisan migrate` 

## Using GUI

* after installation you can access translation gui with `http://app-url.dev/translation`.
* create your languages and translate them.
* you can use (.) dot syntax to defind multidimensional array in lang file like this in `key` field:
```
level1.level2.level3.level4.etc
```
which will produce this result in lang file 
```
'level1' => [
        'level2' => [
            'level3' => [
                'level4' => 'etc',
            ],
        ],
    ],
```


## Database translation 
* To start using database translations in your models use this trait `Motwreen\Translation\Traits\TranslatableTrait`.
* define a protected `$translatable` array in your model class like this :
```
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Motwreen\Translation\Traits\TranslatableTrait;

class Category extends Model
{
    use TranslatableTrait;

    protected $translatable = ['name'];
}

```

And in your controller :
```
public function store(Request $request){
    $category = new App\Category;
    $category->name => ['en'=>'Test Name','du'=>'Miene name ist Test'];
    $category->description => ['en'=>'Test Description','du'=>'Miene name ist Description'];
    $category->save();
}

```