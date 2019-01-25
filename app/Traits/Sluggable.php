<?php
namespace App\Traits;

trait Sluggable
{
    public static function bootSluggable()
    {
        static::saving(function ($model) {
            $model->slug = str_slug($model->title);
        });
    }
    
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
