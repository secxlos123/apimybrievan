<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\ApiLas;
use Illuminate\Http\Request;

class ApiLasController extends Controller
{
    public function index(Request $request) {
    	// print_r($request->header('pn'));exit();
    	$ApiLas  = new ApiLas();
        $pn      = $request->header('pn');
    	$respons = $request->all();
    	$method  = $respons['requestMethod'];
    	$data	 = $respons['requestData'];

    	switch ($method) {
    		case 'insertDataDebtPerorangan':
		        $insert = $ApiLas->insertDataDebtPerorangan($data, $pn);
    			return $insert;
                break;

            case 'insertPrescreeningBriguna':
                $insert = $ApiLas->insertPrescreeningBriguna($data);
                return $insert;
                break;
    	
            case 'insertPrescoringBriguna':
                $insert = $ApiLas->insertPrescoringBriguna($data);
                return $insert;
                break;

            case 'insertDataKreditBriguna':
                $insert = $ApiLas->insertDataKreditBriguna($data);
                return $insert;
                break;

            case 'insertAgunanLainnya':
                $insert = $ApiLas->insertAgunanLainnya($data);
                return $insert;
                break;

            case 'hitungCRSBrigunaKarya':
                $hitung = $ApiLas->hitungCRSBrigunaKarya($data);
                return $hitung;
                break;

    		default:
    			return array('status' => 400, 'message' => 'Uknown request method');
    			break;
    	}
    }
}
