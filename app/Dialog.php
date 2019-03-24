<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dialog extends Model
{
    //
    protected $table = 'dialogs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'my_id',
        'other_id',
        'created_at',
        'updated_at'
    ];
}
