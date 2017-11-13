<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\City;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $cities = City::getLists($request)->paginate($limit);
        return response()->success(['contents' => $cities]);
    }

    /**
     * Display a all of city listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $city = City::all();
        return response()->success(['contents' => $city]);
    }

}
