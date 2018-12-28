<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /** */
    protected $guarded = [];

    /** */
    public function sprints()
    {
        return $this->hasMany(Sprint::class);
    }
}
