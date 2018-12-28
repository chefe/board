<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
    /** */
    protected $guarded = [];

    /** */
    protected $dates = ['start', 'end'];

    /** */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
