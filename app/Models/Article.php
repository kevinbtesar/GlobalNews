<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public $timestamps = true;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'title',
        'image_url',
        'source',
        'app_category',
        'subreddit',
        'created_utc',
        'author',
        'url',
        'reddit_article_id',
        'app_id_fk',
        
        
        
    ];

}




