<?php

namespace App;

use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use LogsActivity;

    /** */
    protected $guarded = [];

    /** */
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = ['updated_at', 'created_at'];

    /** */
    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
