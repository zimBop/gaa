<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Revisionable;
use App\Traits\Sluggable;

class Article extends Model
{
    use Sluggable, Revisionable;
    
    protected $fillable = ['category_id', 'title', 'body', 'slug', 'published', 'created_by', 'modified_by'];
    
    /**
     * The tags associated with the article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }
    
    /**
     * Get the article category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
    
    /**
     * Get article comments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }
}
