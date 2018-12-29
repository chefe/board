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

    /** */
    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    /** */
    public function tasks()
    {
        return $this->hasManyThrough(Task::class, Story::class);
    }
}
