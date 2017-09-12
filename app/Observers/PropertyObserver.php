<?php

namespace App\Observers;

use App\Models\Property;
use App\Models\PropertyType;
use File;
use Storage;

class PropertyObserver
{
    /**
     * Listen to the property created event.
     *
     * @param  Property  $property
     * @return void
     */
    public function created(Property $property)
    {
        if (request()->hasFile('photo')) {
            $path = request()->file('photo')->store('', 'properties');
            $property->photo()->create( compact('path') );
        }
        // $propertyTypes = $property->propertyTypes()->createMany(request('property_types'));
        // $this->createPropertyTypes($propertyTypes, $property);
    }

    /**
     * Listen to the property to create property types.
     *
     * @param  array  $propertyTypes
     * @param  Property $property
     * @return void
     */
    public function createPropertyTypes($propertyTypes, Property $property)
    {
        foreach ($propertyTypes as $key => $propertyType) {
            $images = request("property_types.{$key}.images") ?: [];
            $path = $this->generatePaths($images);
            $photosOfType = $propertyType->photos()->createMany($path);
            $propertyItems = request("property_types.{$key}.property_items");
            $propertyItems = $propertyType->propertyItems()->createMany($propertyItems);
            $this->createPropertyItems($propertyItems, $propertyType, $key);
        }
    }

    /**
     * Listen to the property to create property items.
     *
     * @param  array  $propertyItems
     * @param  PropertyType  $propertyType
     * @param  integer  $index
     * @return void
     */
    public function createPropertyItems($propertyItems, PropertyType $propertyType, $index)
    {
        foreach ($propertyItems as $key => $propertyItem) {
            $images = request("property_types.{$index}.property_items.{$key}.images") ?: [];
            $path = $this->generatePaths($images);
            $photosOfType = $propertyItem->photos()->createMany($path);
        }
    }

    /**
     * Listen for generate path of images.
     *
     * @param  array  $images
     * @param  string  $service
     * @return array
     */
    public function generatePaths($images, $service = 'properties')
    {
        $path = [];
        foreach ($images as $image) {
            $path[] = ['path' => $image->store('', $service)];
        }
        return $path;
    }
}