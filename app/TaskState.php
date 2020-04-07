<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class TaskState extends Model
{
    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
