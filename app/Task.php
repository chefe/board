<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** */
    protected $guarded = [];

    /** */
    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
