<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['title', 'body', 'slug', 'published', 'created_by', 'modified_by'];
    
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->slug = str_slug($model->title);
        });
    }
    
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
