<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class CalculatorController extends Controller
{

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function calculate()
    {
        $params  = $this->request->all();
        $type = $params['type'];

        if($type == 'generateFlat'){
            return $this->generateFlat();
        }else if($type == "generateEfektif"){
            return $this->generateEfektif();
        }else if($type == "generateEfektifFixedFloat"){
            return $this->generateEfektif_FixedFloat();
        }else if($type == "generateEfektifFixedFloorFloat"){
            return $this->generateEfektif_FixedFloorFloat();
        }else{
            return response()->error([
                'message' => "Invalid Type"
            ], 404);
        }
    }

    public function generateFlat()
    {
        $params  = $this->request->all();
        
        $plafond = $params['plafond'];
        $term    = $params['term'];
        $rate    = $params['rate'];

        $n         = $term + 1;
        $returnVal = [];

        $angsuranPokok = $plafond / $term;
        $angsuranBunga = (($rate / 12) /100) * $plafond;

        for($i = 0; $i < $n; $i++)
        {
            if($i == 0){
                $returnVal[0][0] = 0;
                $returnVal[0][1] = 0;
                $returnVal[0][2] = (int)$plafond;
            }else{
                $plafond -= $angsuranPokok;
                $returnVal[$i][0] = floor($angsuranPokok);
                $returnVal[$i][1] = floor($angsuranBunga);
                $returnVal[$i][2] = $plafond;
            }
        }
        return response()->success([
            'contents' => $returnVal
        ]);
    }

    public function generateEfektif()
    {
        $params  = $this->request->all();

        $plafond = $params['plafond'];
        $term    = $params['term'];
        $rate    = $params['rate'];

        $n         = $term + 1;
        $returnVal = [];

        $angsuranTot = round(((($rate / 12) / 100) * $plafond) / (1 - (1 / pow((1.00 + (($rate / 100) / 12.00)), $term))));

        for($i = 0; $i < $n; $i++){
            if($i == 0){
                $returnVal[0][0] = 0;
                $returnVal[0][1] = 0;
                $returnVal[0][2] = round($plafond);
            }else if($i == $n - 1){
                $angsuranBunga = ((($rate / 12) / 100) * $plafond);
                $angsuranPokok = $angsuranTot - $angsuranBunga;

                $plafond -= $angsuranPokok;
                $angsuranPokok += $plafond;
                $angsuranBunga =  $angsuranTot - $angsuranPokok;

                $returnVal[$i][0] = round($angsuranPokok);
                $returnVal[$i][1] = round($angsuranBunga);
                $returnVal[$i][2] = 0;
            }else{
                $angsuranBunga = ((($rate / 12) / 100) * $plafond);
                $angsuranPokok = $angsuranTot - $angsuranBunga;

                $plafond -= $angsuranPokok;
                $returnVal[$i][0] = round($angsuranPokok);
                $returnVal[$i][1] = round($angsuranBunga);
                $returnVal[$i][2] = round($plafond);
            }
        }

        return response()->success([
            'contents' => $returnVal
        ]);
    }

    public function generateEfektif_FixedFloat()
    {
        $params  = $this->request->all();

        $plafond  = $params['price'];
        $fxflterm = $params['fxflterm'];
        $fxterm   = $params['fxterm'];
        $fxrate   = $params['fxrate'];
        $flrate   = $params['flrate'];

        $n = $fxflterm + 1;
        $returnVal = [];

        $angsuranTotFix = ((($fxrate / 12) / 100) * $plafond) / (1 - (1 / pow((1.00 + ($fxrate / 100) / 12.00), $fxflterm)));
        for($i = 0; $i < $fxterm + 1; $i++){
            if($i == 0){
                $returnVal[0][0] = 0;
                $returnVal[0][1] = 0;
                $returnVal[0][2] = (int)$plafond;
            }else{
                $angsuranBunga = (($fxrate / 12) / 100) * $plafond;
                $angsuranPokok = $angsuranTotFix - $angsuranBunga;

                $plafond -= (int)$angsuranPokok;
                $returnVal[$i][0] = round($angsuranPokok);
                $returnVal[$i][1] = round($angsuranBunga);
                $returnVal[$i][2] = round($plafond);
            }
        }

        $angsuranTotFloat = ((($flrate / 12) / 100) * $plafond) / (1 - (1 / pow((1.00 + (($flrate / 100) / 12.00)), ($fxflterm - $fxterm))));
        
        for($i = $fxterm + 1; $i < $n; $i++){
            if($i == $fxflterm){
                $angsuranBunga = (($flrate / 12) / 100) * $plafond;
                $angsuranPokok = $angsuranTotFloat - $angsuranBunga;

                $plafond -= $angsuranPokok;
                $angsuranPokok += $plafond;
                $angsuranBunga =  $angsuranTotFloat - $angsuranBunga;
                
                $returnVal[$i][0] = round($angsuranPokok);
                $returnVal[$i][1] = round($angsuranBunga);
                $returnVal[$i][2] = 0;
            }else{
                $angsuranBunga = (($flrate / 12) / 100) * $plafond;
                $angsuranPokok = $angsuranTotFloat - $angsuranBunga;

                $plafond -= $angsuranPokok;
                $returnVal[$i][0] = round($angsuranPokok);
                $returnVal[$i][1] = round($angsuranBunga);
                $returnVal[$i][2] = round($plafond);
            }
        }

        return response()->success([
            'contents' => $returnVal
        ]);
    }

    public function generateEfektif_FixedFloorFloat()
    {
        $params  = $this->request->all();

        $plafond     = $params['price'];
        $fxflflterm  = $params['fxflflterm'];
        $ffxterm     = $params['ffxterm'];
        $fflterm     = $params['fflterm'];
        $ffxrate     = $params['ffxrate'];
        $ffloorrate  = $params['ffloorrate'];
        $ffloatlrate = $params['ffloatlrate'];

        $n = $fxflflterm + 1;
        $returnVal = [];

        $angsuranTotFix = ((($ffxrate / 12) / 100) * $plafond) / (1 - (1 / pow(1.00 + (($ffxrate / 100) / 12.00), $fxflflterm)));
        for($i = 0; $i < $ffxterm + 1; $i++){
            if($i == 0){
                $returnVal[0][0] = 0;
                $returnVal[0][1] = 0;
                $returnVal[0][2] = (int)$plafond;
            }else{
                $angsuranBunga = (($ffxrate / 12) / 100) * $plafond;
                $angsuranPokok = $angsuranTotFix - $angsuranBunga;

                $plafond -= $angsuranPokok;
                $returnVal[$i][0] = round($angsuranPokok);
                $returnVal[$i][1] = round($angsuranBunga);
                $returnVal[$i][2] = round($plafond);
            }
        }

        $angsuranTotFloor = ((($ffloorrate / 12) / 100) * $plafond) / (1 - (1 / pow((1.00 + (($ffloorrate / 100) / 12.00)), ($fxflflterm - $ffxterm))));

        for($i = $ffxterm + 1; $i < $fflterm + 1; $i++){
            $angsuranBunga = (($ffloorrate / 12) / 100) * $plafond;
            $angsuranPokok = $angsuranTotFloor - $angsuranBunga;

            $plafond -= $angsuranPokok;
            $returnVal[$i][0] = round($angsuranPokok);
            $returnVal[$i][1] = round($angsuranBunga);
            $returnVal[$i][2] = round($plafond);
        }

        $angsuranTotFloat = ((($ffloatlrate / 12) / 100) * $plafond) / (1 - (1 / pow(1.00 + (($ffloatlrate / 100) / 12.00), ($fxflflterm - $fflterm))));

        for ($i = $fflterm + 1; $i < $n; $i++)
        {
           if ($i == $fxflflterm)
           {
                $angsuranBunga = (($ffloatlrate / 12) / 100) * $plafond;
                $angsuranPokok = $angsuranTotFloat - $angsuranBunga;
                $plafond -= $angsuranPokok;
                $angsuranPokok += $plafond;
                $angsuranBunga = $angsuranTotFloat - $angsuranPokok;
                $returnVal[$i][0] = round($angsuranPokok);
                $returnVal[$i][1] = round($angsuranBunga);
                $returnVal[$i][2] = 0;
            }else{
                $angsuranBunga = (($ffloatlrate / 12) / 100) * $plafond;
                $angsuranPokok = $angsuranTotFloat - $angsuranBunga;
                $plafond -= $angsuranPokok;
                $returnVal[$i][0] = round($angsuranPokok);
                $returnVal[$i][1] = round($angsuranBunga);
                $returnVal[$i][2] = round($plafond);
            }
        }
        return response()->success([
            'contents' => $returnVal
        ]);        
    }
}
