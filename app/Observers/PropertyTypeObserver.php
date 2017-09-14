<?php

namespace App\Observers;

use App\Models\PropertyType;

class PropertyTypeObserver
{
    /**
     * Listen to the property_type saved event.
     *
     * @param  PropertyType  $property_type
     * @return void
     */
    public function saved(PropertyType $property_type)
    {
        /**
         * This logic for remove image for property type
         */
        removed_photos($property_type);

        /**
         * Call function generate_paths on helpers file
         * request photos is array type, properties is a drive for saving to storage, last variable is folder
         */
        if (request()->hasFile('photos')) {
            $paths = generate_paths(request('photos'), 'properties', $property_type->id);
            $property_type->photos()->createMany($paths);
        }
    }
}