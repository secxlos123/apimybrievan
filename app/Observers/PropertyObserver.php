<?php

namespace App\Observers;

use App\Models\Property;
use Storage;

class PropertyObserver
{
    /**
     * Listen to the property created event.
     *
     * @param  Property  $property
     * @return void
     */
    public function saved(Property $property)
    {
        if (request()->hasFile('photo') && $property->photo) Storage::delete($property->photo->path);

        if (request()->hasFile('photo')) {
            $path = request()->file('photo')->store('', 'properties');
            $property->photo()->create( compact('path') );
        }
    }
}