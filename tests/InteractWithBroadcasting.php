<?php

namespace Tests;

use Illuminate\Support\Facades\Broadcast;

trait InteractWithBroadcasting
{
    /** */
    protected function expectBroadcast($class)
    {
        Broadcast::spy()
            ->shouldReceive('event')
            ->once()
            ->withArgs(function ($e) use ($class) {
                return (get_class($e) == $class);
            });
    }

    /** */
    protected function expectBroadcastWithId($class, $id, $property)
    {
        Broadcast::spy()
            ->shouldReceive('event')
            ->once()
            ->withArgs(function ($e) use ($class, $id, $property) {
                return (get_class($e) == $class) &&
                    ($e->$property->id == $id);
            });
    }

    /** */
    protected function expectNoBroadcast()
    {
        Broadcast::spy()->shouldReceive('event')->never();
    }
}
