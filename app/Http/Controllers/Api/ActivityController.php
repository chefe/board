<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index()
    {
        $pagination = Auth::user()->actions()->latest()->paginate(36)->toArray();

        $pagination['data'] = collect($pagination['data'])->map(function ($activity) {
            $changes = $activity['properties']->toArray();

            if (array_key_exists('old', $changes) && array_key_exists('attributes', $changes)) {
                $keys = collect(array_keys($changes['attributes']));

                $changes = $keys->reject(function ($key) use ($changes) {
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
                'changes' => $changes,
            ];
        })->all();

        return $pagination;
    }
}
