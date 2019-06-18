# Laravel Translation
A simple GUI for Laravel Translation.

This package will allows you to take control of `lang` files in Laravel infrastructre .
it will also allows you to translate user inputs (models or database columns data)

## Installation
So simple just run `composer require motwreen/translation` in your application terminal.

#### after installation you can access translation gui with `http://app-url.dev/translation`.

## Publish
If you'd like to edit package views run `php artisan vendor:publish --tag=motwreen-translation` in your terminal and you'll find it in this path : `resources/views/vendor/translation`.

## Migrate
To migrate database tables run `php artisan migrate` 

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

##TODO:
* Fix return appended data after addd new records
* Copy All language files from default lang to newely created lang