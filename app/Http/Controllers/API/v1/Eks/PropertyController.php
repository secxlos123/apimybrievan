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
        if ( ! $developerId && $request->user() ) 
            $developerId = $request->user()->inRole('developer') ? $request->user()->id : null;
        
        $limit = $request->input('limit') ?: 10;
        $properties = Property::getLists($request, $developerId)->paginate($limit);

        $properties->transform(function ($prop) {
            $props = $prop->toArray();
            $props['prop_photo'] = $prop->propPhoto ? $prop->propPhoto->image : null;
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
        return $this->storeOrUpdate($request, []);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Property $property)
    {
        $prop = $property->load('photo', 'developer')->toArray();
        $developer = $property->developer;
        $prop['photo'] = $property->photo ? $property->photo->image : null;
        $prop['developer_name'] = $developer->company_name;
        $prop['developer_logo'] = $developer->user->image;
        unset($prop['developer']);
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
        return $this->storeOrUpdate($request, $property);
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
     * Handling store and update property
     * 
     * @param  Request $request  [description]
     * @param  \App\Models\Property|array  $property [description]
     * @return \Illuminate\Http\Response
     */
    public function storeOrUpdate(Request $request, $property)
    {
        \DB::beginTransaction();
        try {
            if ( ! $property instanceof Property ) {
                $property = Property::create($request->all());
                $code = 201; $method = 'disimpan';
            } else {
                $property->update($request->all());
                $code = 200; $method = 'dirubah';
            }

            // this logic for saving data to internal bri
            if ($this->service($property)) {
                $status = 'success'; $message = "Project {$property->name} berhasil {$method}.";
                \DB::commit();
            } else {
                throw new \Exception("Error Processing Request", 422);
            }
            
        } catch (\Exception $e) {
            \DB::rollBack();
            $status = 'error'; $message = "Project {$request->input('name')} gagal {$method}.";
            $code = $e->getCode();
        }

        return response()->{$status}(compact('message'), $code);
    }

    /**
     * Submit to service BRI
     * 
     * @return void
     */
    public function service($property)
    {
        $property->load('developer');

        $current = [
            'tipe_project' => 'KPR',
            'nama_project' => $property->name,
            // 'alamat_project' => $property->address,
            'pic_project' => $property->pic_name ?: '',
            'pks_project' => $property->developer->pks_number ?: '-',
            'deskripsi_project' => $property->description,
            'telepon_project' => $property->pic_phone ?: '',
            'hp_project' => $property->pic_project ?: '',
            'fax_project' => '', 
            'deskripsi_pks_project' => $property->developer->pks_description ?: '',
            'project_value' => $property->prop_id_bri ?: '',
        ];

        $id = \Asmx::setEndpoint('InsertDataProject')
            ->setBody(['request' => json_encode($current)])
            ->post('form_params');
        \Log::info($id);

        if ($id['code'] == 200) {
            $property->update(['prop_id_bri' => $id['contents']]);
            return true;
        }

        return false;        
    }

    /**
     * Get properties by nearby location
     * 
     * @param  Request $request 
     * @return \Illuminate\Http\Response          
     */
    public function nearby(Request $request)
    {
        $properties = Property::nearby($request);
        return response()->success(['contents' => $properties]);
    }
}
