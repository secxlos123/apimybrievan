<?php

namespace App\Observers;

use App\Models\PropertyItem;

class PropertyItemObserver
{
    /**
     * Listen to the property_item saved event.
     *
     * @param  PropertyType  $property_item
     * @return void
     */
    public function saved(PropertyItem $property_item)
    {
        /**
         * This logic for saving many photos
         */
        saving_photos($property_item);
    }
}