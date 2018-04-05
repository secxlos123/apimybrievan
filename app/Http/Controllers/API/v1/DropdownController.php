<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Dropdown\PropItemRequest;
use App\Http\Requests\API\v1\Dropdown\PropTypesRequest;
use App\Http\Requests\API\v1\Dropdown\PropertyRequest;
use App\Models\Property;
use App\Models\PropertyItem;
use App\Models\PropertyType;
use App\Models\Developer;
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

    /**
     * [types description]
     * @param Request $request [description]
     * @return [type]
     */
    public function list_developer(Request $request)
    {
        $limit = $request->input('limit');
        $list_developer = Developer::getListsDeveloper($request)->paginate($limit);
        return response()->success(['contents' => $list_developer]);
    }

    /**
     * [types description]
     * @param Request $request [description]
     * @return [type]
     */
    public function list_proptype(Request $request)
    {
        $limit = $request->input('limit');
        $list_proptype = PropertyType::getListsPropType($request)->paginate($limit);
        return response()->success(['contents' => $list_proptype]);
    }
}
