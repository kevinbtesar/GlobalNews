<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    public $timestamps = true;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'article_id_fk'
    ];

    public function articleData()
    {
        return $this->belongsTo(Article::class, 'article_id_fk');
    }


}
