<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Dropdown\PropItemRequest;
use App\Http\Requests\API\v1\Dropdown\PropTypesRequest;
use App\Http\Requests\API\v1\Dropdown\PropertyRequest;
use App\Models\Property;
use App\Models\PropertyItem;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class DropdownController extends Controller
{
	/**
	 * [properties description]
	 * 
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function properties(PropertyRequest $request)
    {
    	$properties = Property::getLists($request, null)->paginate($request->input('limit'));
    	return response()->success(['contents' => $properties]);
    }

    /**
     * [types description]
     * 
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function types(PropTypesRequest $request)
    {
        $propertyTypes = PropertyType::getLists($request)->paginate($request->input('limit'));
        return response()->success(['contents' => $propertyTypes]);
    }

    /**
     * [types description]
     * 
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function items(PropItemRequest $request)
    {
        $propertyItems = PropertyItem::getLists($request)->paginate($request->input('limit'));
        return response()->success(['contents' => $propertyItems]);
    }
}
