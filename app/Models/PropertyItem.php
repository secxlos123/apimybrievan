<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class PropertyItem extends Model implements AuditableContract
{
    use Auditable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_type_id', 'address', 'price', 'is_available', 'status', 'available_status','no_item'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_available' => 'integer'
    ];

    protected $appends = [
      'photos','property_type_name'
    ];

    /**
     * The attributes that are rules for validations.
     *
     * @var array
     */
    public static $rules = [
        'address' => 'required',
        'status'  => 'required|in:new,second',
        'price'   => 'required|numeric',
        //'first_unit'   => 'required|numeric',
        //'last_unit'   => 'required|numeric',
        //'unit_size'   => 'required|numeric',
        'photos.*'=> 'image|max:5024',
    ];

    /**
     * Get the Type Name for Property.
     *
     * @return string
     */
    public function getPropertyTypeNameAttribute()
    {
        $type = PropertyType::find($this->property_type_id);
        $name=NULL;
        if (count($type)>0) {
            $name = $type->name;
        }
        return $name;
    }

    public function getPhotosAttribute()
    {
      return $this->photos()->get();
    }

    /**
     * Get parent property of developer.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function propertyType()
    {
        return $this->belongsTo( PropertyType::class, 'property_type_id' );
    }

    /**
     * Get the properties photos.
     */
    public function photos()
    {
        return $this->morphMany( Photo::class, 'photoable' );
    }

    /**
     * Get lists of property type
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param  Request $request [description]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetLists($query, Request $request)
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['property_items.id', 'asc'];
        $select = $request->has('dropdown') ? ['property_items.id', 'property_items.address', 'property_items.price'] : array_merge(['property_items.id'],['property_items.property_type_id','property_items.no_item', 'property_items.address', 'property_items.price', 'property_items.is_available', 'property_items.status']);
        
        if ( ! $request->has('dropdown') ) $query->with('photos');

        $data = $query
            ->with('propertyType')
            ->where(function ($item) use (&$request) {
                if ($request->has('without_independent')){
                    if ($request->without_independent) {
                    $data = \DB::table('properties')->select('id')->whereNotIn('developer_id',[1])->get();
                    $dataid = array();
                    if (count($data)>0) {
                        foreach ($data as $key => $value) {
                            foreach ($value as $key2 => $value2) {
                                $dataid[]= $value2;
                            }
                        }
                    }
                    $dataitem = \DB::table('property_types')->select('id')->whereIn('property_id',$dataid)->get();
                    $itemid = array();
                    if (count($dataitem)) {
                        foreach ($dataitem as $key => $value) {
                            foreach ($value as $key2 => $value2) {
                                $itemid[]= $value2;
                            }
                        }
                    }
                    $item->whereIn('property_type_id',$itemid);
                    }
                }
                if ($request->has('property_id')){
                    $propitem = \DB::table('property_types')->select('id')->where('property_id',$request->input('property_id'))->get();
                    $propid = array();
                    if (count($propitem)) {
                        foreach ($propitem as $key => $value) {
                            foreach ($value as $key2 => $value2) {
                                $propid[]= $value2;
                            }
                        }
                    }
                    $item->whereIn('property_items.property_type_id', $propid);
                }
                if ($request->has('property_type_id')) 
                    $item->where('property_items.property_type_id', $request->input('property_type_id'));

                if ($request->has('is_available')) 
                    $item->where('property_items.is_available', $request->input('is_available'));

                if ($request->has('status')) 
                    $item->where('property_items.status', $request->input('status'));

                if ($request->has('price')) 
                    $item->whereBetween('property_items.price', explode('|', $request->input('price')));
            })
            ->where(function ($item) use (&$request, &$query) {
                if ($request->has('search')) $query->search($request);
            })
            ->whereHas('propertyType', function ($type) use (&$request) {
                if ($user = $request->user()) {
                    if ($user->inRole('developer')) {
                        $developerId = $user->developer->id;
                        $type->developerOwned($developerId);
                    }
                }
            })
            ->select($select)
            ->selectRaw(" 
                (select developers.user_id from developers where developers.id = (select developers.id from developers where developers.id = 
                (select properties.developer_id from properties where properties.id = 
                (select property_types.property_id from property_types where property_types.id = property_type_id
                )))) as developer_id,
                (select developers.company_name from developers where developers.id = (select developers.id from developers where developers.id = 
                (select properties.developer_id from properties where properties.id = 
                (select property_types.property_id from property_types where property_types.id = property_type_id
                )))) as developer_name,
                (select developers.dev_id_bri from developers where developers.id = (select developers.id from developers where developers.id = 
                (select properties.developer_id from properties where properties.id = 
                (select property_types.property_id from property_types where property_types.id = property_type_id
                )))) as dev_id_bri,
                (select properties.status from properties where developer_id =(select developers.user_id from developers where developers.id = (select developers.id from developers where developers.id = 
                (select properties.developer_id from properties where properties.id = 
                (select property_types.property_id from property_types where property_types.id = property_type_id
                )))) limit 1 ) as prop_status,
                (select properties.is_approved from properties where properties.developer_id = (select developers.id from developers where developers.id = (select properties.developer_id from properties where properties.id = (select property_types.property_id from property_types where property_types.id = property_type_id ))) limit 1 ) as is_approved, property_items.available_status")
            ->orderBy($sort[0], $sort[1]);
            // \Log::info("=================query property unit=====================");
            // \Log::info($query->toSql());
            return $data;
    }

    /**
     * Scope a query for search user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, Request $request)
    {
        return $query
            ->where('address', 'ilike', "%{$request->input('search')}%")
            ->orWhere(\DB::raw('CAST(price as varchar)'), 'ilike', "%{$request->input('search')}%")
            ->orWhereHas('propertyType', function ($type) use (&$request) {
                $type->where('property_types.name', 'ilike', "%{$request->input('search')}%");
            });
    }

    /**
     * Update availibility status
     *
     * @param \Illuminate\Http\Request $request
     */
    public static function setAvailibility( $id, $status )
    {
        $target = static::find( $id );

        if ($target) {
            $target->update([
                'is_available' => ( $status == "available" ? true : false )
                , 'available_status' => $status
            ]);
        }
    }
}
