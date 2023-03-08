<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'title',
        'app_id_android',
        'app_id_ios',
        'store_url_android',
        'store_url_ios',
    ];
}
