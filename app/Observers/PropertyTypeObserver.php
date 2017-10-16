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
         * This logic for saving many photos
         */
        saving_photos($property_type, 'types');
    }
}