<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Present extends Model
{
    //
    protected $table = 'presents';

    protected $fillable = [
        'name',
        'price',
        'image',
    ];


}
