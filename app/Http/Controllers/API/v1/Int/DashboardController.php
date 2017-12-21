<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EForm;
use App\Models\Property;
use App\Models\Customer;
class DashboardController extends Controller
{
	protected $request;
	protected $mEForm;
	protected $mProperty;
	protected $mCustomer;

    public function __construct(Request $request)
    {
    	$this->request   = $request;
    	$this->mEForm    = new EForm;
    	$this->mProperty = new Property;
    	$this->mCustomer = new Customer;
    }

    public function getDataDashboardInternal()
    {
    	$params = $this->request->all();
    	if($params['role'] == "ao" || $params['role'] == "pinca" || $params['role'] == 'mp'){
	    	$startList    = (isset($params['startList'])) ? $params['startList'] : '';
	    	$endList      = (isset($params['endList'])) ? $params['endList'] : '';
	    	$startChart   = (isset($params['startChart'])) ? $params['startChart'] : '';
	    	$endChart     = (isset($params['endChart'])) ? $params['endChart'] : '';

	    	$list  = $this->mEForm->getNewestEForm($startList, $endList);
	    	$chart = $this->mEForm->getChartEForm($startChart, $endChart);
			return response()->success([
				'contents' => [
					'list'  => $list,
					'chart' => $chart
				]
			]);
    	}else{
	    	$startChart1 = (isset($params['startChart1'])) ? $params['startChart1'] : '';
	    	$endChart1   = (isset($params['endChart1'])) ? $params['endChart1'] : '';

	    	$startChart2 = (isset($params['startChart2'])) ? $params['startChart2'] : '';
	    	$endChart2   = (isset($params['endChart2'])) ? $params['endChart2'] : '';

	    	$cityId 	 = empty($params['city_id']) ? '' : $params['city_id'];

	    	$listProperty   = $this->mProperty->getNewestProperty($cityId);
	    	$chartProperty  = $this->mProperty->chartNewestProperty($startChart1, $endChart1);
			
			$listCustomer   = $this->mCustomer->newestCustomer();
			$chartCustomer 	= $this->mCustomer->chartNewestCustomer($startChart2, $endChart2);
			return response()->success([
				'contents' => [
					'property'  => [
						'list'  => $listProperty,
						'chart' => $chartProperty,
					],
					'customer'  => [
						'list'  => $listCustomer,
						'chart' => $chartCustomer
					]
				]
			]);
    	}
    }
}
