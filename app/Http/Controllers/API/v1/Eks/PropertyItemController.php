<?php

namespace App\Http\Controllers\API\v1\Eks;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\PropertyItem\CreateRequest;
use App\Models\PropertyItem;
use App\Models\PropertyType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PropertyItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $propertyItems = PropertyItem::getLists($request)->paginate($limit);
        $propertyItems->transform(function ($propItem) use (&$request) {
            $items = $propItem->toArray();

            if ( ! $request->has('dropdown') ) {
                $items['property_type_name'] = $propItem->propertyType->name;
                $items['photos'] = $propItem->photos->transform(function ($photo) {
                    return $photo->image;
                });
            }

            unset($items['property_type']);
            return $items;
        });
        return response()->success(['contents' => $propertyItems]);
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
            for ($i=intval($request->first_unit); $i <= intval($request->last_unit); $i++)
            {
            $propertyItem = PropertyItem::create($request->except('unit_size','first_unit','last_unit')+['no_item'=> $i ]);
            $status = 'success'; $message = "Project Item {$propertyItem->name} berhasil disimpan.";
            $code = 201;
            \DB::commit();
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            $status = 'error'; $message = "Project Item {$request->input('name')} gagal disimpan.";
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
    public function show(PropertyItem $property_item)
    {
        $prop = $property_item->load('propertyType.property.developer', 'photos')->toArray();
        $prop['photos'] = $property_item->photos->transform(function ($photo) {
            return $photo->image;
        });
        $prop['property_id']   = $prop['property_type']['property_id'];
        $prop['property_name'] = $prop['property_type']['property']['name'];
        $prop['property_type_name'] = $prop['property_type']['name'];
        $prop['developer_id'] = $prop['property_type']['property']['developer']['id'];
        $id = $prop['property_type']['property']['developer']['user_id'];
        $user = \App\Models\User::findOrFail($id);
        $prop['developer_name'] = $user->first_name.' '.$user->last_name;
        unset($prop['property_type']);
        return response()->success(['contents' => $prop]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showPropertyType($property_type, PropertyItem $property_item)
    {
        $propType = PropertyType::findBySlug($property_type);

        if ($propType->id === $property_item->property_type_id)
            return $this->show($property_item);

        throw new ModelNotFoundException;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateRequest $request, PropertyItem $property_item)
    {
        \DB::beginTransaction();
        try {
            $property_item->update($request->all());
            $status = 'success'; $message = "Project Item {$property_item->name} berhasil dirubah.";
            $code = 200;
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $status = 'error'; $message = "Project Item {$request->input('name')} gagal dirubah.";
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
    public function updatePropertyType(CreateRequest $request, $property_type, PropertyItem $property_item)
    {
        $propType = PropertyType::findBySlug($property_type);

        if ($propType->id === $property_item->property_type_id)
            return $this->update($request, $property_item);

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

    /**
     * [GetAllType description]
     */
    public function GetAllItem(Request $request)
    {
        $developer_id = env('DEVELOPER_KEY',1);
        $limit = $request->has('limit') ? $request->input('limit') : 500;
        $items = \DB::table('property_items')->selectRaw('id,property_type_id,address,is_available,available_status')->whereRaw('property_type_id in ( select id from property_types where property_id in (select id from properties where developer_id != ? and is_approved = true))',[$developer_id])->paginate($limit);
            return response()->success([
                'message'  => "List Data Semua Item Property",
                'contents' => $items ]);
    }
}
