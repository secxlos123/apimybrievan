<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Property;
class PropertyController extends Controller
{
    protected $request;
    protected $model;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->model   = new Property;
    }

	public function newestProperty()
	{
		$params = $this->request->all();
		$cityId = empty($params['city_id']) ? '' : $params['city_id'];
		$data   = $this->model->getNewestProperty($cityId);

        return response()->success([
            'contents' => $data
        ]);
	}

	public function chartNewestProperty()
	{
		$params 	  = $this->request->all();
    	$startChart   = (isset($params['startChart'])) ? $params['startChart'] : '';
    	$endChart     = (isset($params['endChart'])) ? $params['endChart'] : '';

		$data 	  	  = $this->model->chartNewestProperty($startChart, $endChart);
		return response()->success([
			'contents' => $data
		]);
	}
}
