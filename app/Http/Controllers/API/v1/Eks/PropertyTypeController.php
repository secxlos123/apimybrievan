<?php

namespace App\Http\Controllers\API\v1\Eks;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\PropertyType\CreateRequest;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $propertyTypes = PropertyType::getLists($request)->paginate($limit);
        $propertyTypes->transform(function ($propType) {
            $types = $propType->toArray();
            $types['photos'] = $propType->photos->transform(function ($photo) {
                return $photo->image;
            });
            return $types;
        });
        return response()->success(['contents' => $propertyTypes]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        \DB::beginTransaction();
        try {
            $propertyType = PropertyType::create($request->all());
            $status = 'success'; $message = "Project Type {$propertyType->name} berhasil disimpan.";
            $code = 201;
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $status = 'error'; $message = "Project Type {$request->input('name')} gagal disimpan.";
            $code = 500;
        }
        return response()->{$status}(compact('message'), $code);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $prop = PropertyType::with('photos')
            ->withCount('propertyItems as items')
            ->whereSlug($slug)
            ->first();

        $prop->photos = $prop->photos->transform(function ($photo) {
            return $photo->image;
        });

        $property = $prop->property;
        $others = [
            'property_name'    => $property->name,
            'property_address' => $property->address,
            'developer_name'   => $property->developer->company_name,
            'developer_logo'   => $property->developer->user->image,
        ];

        return response()->success([
            'contents' => array_merge(array_except($prop->toArray(), 'property'), $others)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showProperty($property, PropertyType $property_type)
    {
        $prop = Property::findBySlug($property);

        if ($prop->id === $property_type->property_id)
            return $this->show($property_type);

        throw new ModelNotFoundException;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateRequest $request, PropertyType $property_type)
    {
        \DB::beginTransaction();
        try {
            $property_type->update($request->all());
            $status = 'success'; $message = "Project Type {$property_type->name} berhasil disimpan.";
            $code = 200;
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $status = 'error'; $message = "Project Type {$request->input('name')} gagal disimpan.";
            $code = 500;
        }
        return response()->{$status}(compact('message'), $code);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProperty(CreateRequest $request, $property, PropertyType $property_type)
    {
        $prop = Property::findBySlug($property);

        if ($prop->id === $property_type->property_id)
            return $this->update($request, $property_type);

        throw new ModelNotFoundException;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
