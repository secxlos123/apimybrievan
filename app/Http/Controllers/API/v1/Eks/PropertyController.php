<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Property\CreateRequest;
use App\Models\Property;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $developerId = null)
    {
        if ( ! $developerId && $request->user()->inRole('developer') ) $developerId = $request->user()->id;
        $limit = $request->input('limit') ?: 10;
        $properties = Property::getLists($request, $developerId)->paginate($limit);

        $properties->transform(function ($prop) {
            $props = $prop->toArray();
            $props['prop_photo'] = $prop->propPhoto->image;
            return $props;
        });

        return response()->success(['contents' => $properties]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\API\v1\Property\CreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        \DB::beginTransaction();
        try {
            $property = Property::create($request->all());
            $status = 'success'; $message = "Project {$property->name} berhasil disimpan.";
            $code = 201;
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $status = 'error'; $message = "Project {$request->input('name')} gagal disimpan.";
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
    public function show(Property $property)
    {
        $prop = $property->load('photo')->toArray();
        $prop['photo'] = $property->photo->image;
        return response()->success(['contents' => $prop]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateRequest $request, Property $property)
    {
        \DB::beginTransaction();
        try {
            $property->update($request->all());
            $status = 'success'; $message = "Project {$property->name} berhasil disimpan.";
            $code = 200;
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $status = 'error'; $message = "Project {$request->input('name')} gagal disimpan.";
            $code = 500;
        }
        return response()->{$status}(compact('message'), $code);
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
