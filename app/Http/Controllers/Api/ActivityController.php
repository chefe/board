<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ActivityController extends Controller
{
    /** */
    public function index()
    {
        $pagination = Auth::user()->actions()->latest()->paginate(36)->toArray();

        $pagination['data'] = collect($pagination['data'])->map(function ($activity) {
            $changes = $activity['properties']->toArray();

            if (array_key_exists('attributes', $changes)) {
                $keys = collect(array_keys($changes['attributes']));

                $changes = $keys->filter(function ($key) use ($changes) {
                    return array_key_exists('old', $changes);
                })->reject(function ($key) use ($changes) {
                    return $changes['attributes'][$key] == $changes['old'][$key];
                })->mapWithKeys(function ($key) use ($changes) {
                    return [$key => [
                        'new' => $changes['attributes'][$key],
                        'old' => $changes['old'][$key],
                    ]];
                });
            }

            return [
                'date' => $activity['created_at'],
                'description' => $activity['description'],
                'subject_type' => strtolower(str_replace('App\\', '', $activity['subject_type'])),
                'subject_properties' => $activity['properties']['attributes'] ?? [],
                'changes' => $changes
            ];
        })->all();

        return $pagination;
    }
}
