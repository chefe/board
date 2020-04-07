<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $guarded = [];

    public function sprints()
    {
        return $this->hasMany(Sprint::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
