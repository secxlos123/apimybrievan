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
        
        $price       = $params['price'];
        $term        = $params['term'];
        $rate        = $params['rate'];
        $downPayment = $params['downPayment'];
        $total       = $price;

        $plafond   = $price - $downPayment;
        $plafonds  = $plafond;
        $n         = $term + 1;
        $returnVal = [];

        $angsuranPokok = $plafond / $term;
        $angsuranBunga = (($rate / 12) /100) * $plafond;
        $angsuran      = (round($angsuranPokok) + round($angsuranBunga));

        for($i = 0; $i < $n; $i++)
        {
            if($i == 0){
                $returnVal[$i] = [
                    "bulan"          => $i,
                    "sisa_pinjaman"  => (int)$plafond,
                    "angsuran_pokok" => 0,
                    "angsuran_bunga" => 0,
                    "angsuran"       => 0,
                    "bunga"          => "0%",
                ];
            }else{
                $plafond -= $angsuranPokok;
                $returnVal[$i] = [
                    "bulan"          => $i,
                    "sisa_pinjaman"  => round($plafond),
                    "angsuran_pokok" => round($angsuranPokok),
                    "angsuran_bunga" => round($angsuranBunga),
                    "angsuran"       => $angsuran,
                    "bunga"          => $rate."%",
                ];
            }
        }

        $rincian = [
            "rincian" => [
                "plafond"       => $plafonds,
                "uang_muka"     => $downPayment,
                "suku_bunga"    => $rate."%",
                "kredit_fix"    => $term." Bulan",
                "lama_pinjaman" => $term." Bulan",
            ],
            "angsuran_perbulan"  => $angsuran,
            "pembayaran_pertama" => $downPayment,
        ];

        return response()->success([
            'contents' => [
                'rincian_pinjaman' => $rincian,
                'detail_angsuran'  => $returnVal,
            ]
        ]);
    }

    public function generateEfektif()
    {
        $params  = $this->request->all();

        $price       = $params['price'];
        $term        = $params['term'];
        $rate        = $params['rate'];
        $downPayment = $params['downPayment'];
        $total       = $price;

        $plafond   = $price - $downPayment;
        $plafonds  = $plafond;
        $n         = $term + 1;
        $returnVal = [];

        $angsuranTot = round(((($rate / 12) / 100) * $plafond) / (1 - (1 / pow((1.00 + (($rate / 100) / 12.00)), $term))));

        for($i = 0; $i < $n; $i++){
            if($i == 0){
                $returnVal[$i] = [
                    "bulan"          => $i,
                    "sisa_pinjaman"  => round($plafond),
                    "angsuran_pokok" => 0,
                    "angsuran_bunga" => 0,
                    "angsuran"       => 0,
                    "bunga"          => "0%",
                ];
            }else if($i == $n - 1){
                $angsuranBunga = ((($rate / 12) / 100) * $plafond);
                $angsuranPokok = $angsuranTot - $angsuranBunga;

                $plafond -= $angsuranPokok;
                $angsuranPokok += round($plafond);
                $angsuranBunga =  $angsuranTot - $angsuranPokok;

                $returnVal[$i] = [
                    "bulan"          => $i,
                    "sisa_pinjaman"  => round($plafond),
                    "angsuran_pokok" => round($angsuranPokok),
                    "angsuran_bunga" => round($angsuranBunga),
                    "angsuran"       => round($angsuranPokok) + round($angsuranBunga),
                    "bunga"          => $rate."%",
                ];

                $angsuran = round($angsuranPokok) + round($angsuranBunga);

            }else{
                $angsuranBunga = ((($rate / 12) / 100) * $plafond);
                $angsuranPokok = $angsuranTot - $angsuranBunga;
                $plafond -= $angsuranPokok;

                $returnVal[$i] = [
                    "bulan"          => $i,
                    "sisa_pinjaman"  => round($plafond),
                    "angsuran_pokok" => round($angsuranPokok),
                    "angsuran_bunga" => round($angsuranBunga),
                    "angsuran"       => round($angsuranPokok) + round($angsuranBunga),
                    "bunga"          => $rate."%",
                ];
            }
        }

        $rincian = [
            "rincian" => [
                "plafond"       => $plafonds,
                "uang_muka"     => $downPayment,
                "suku_bunga"    => $rate."%",
                "kredit_fix"    => $term." Bulan",
                "lama_pinjaman" => $term." Bulan",
            ],
            "angsuran_perbulan"  => $angsuran,
            "pembayaran_pertama" => $downPayment,
        ];

        return response()->success([
            'contents' => [
                'rincian_pinjaman' => $rincian,
                'detail_angsuran'  => $returnVal,
            ]
        ]);
    }

    public function generateEfektif_FixedFloat()
    {
        $params  = $this->request->all();

        $price       = $params['price'];
        $fxflterm    = $params['fxflterm'];
        $fxterm      = $params['fxterm'];
        $fxrate      = $params['fxrate'];
        $flrate      = $params['flrate'];
        $downPayment = $params['downPayment'];
        $total       = $price;

        $plafond     = $price - $downPayment;
        $plafonds    = $plafond;
        $n           = $fxflterm + 1;
        $returnVal   = [];

        $angsuranTotFix = ((($fxrate / 12) / 100) * $plafond) / (1 - (1 / pow((1.00 + ($fxrate / 100) / 12.00), $fxflterm)));
        for($i = 0; $i < $fxterm + 1; $i++){
            if($i == 0){
                $returnVal[$i] = [
                    "bulan"          => $i,
                    "sisa_pinjaman"  => round($plafond),
                    "angsuran_pokok" => 0,
                    "angsuran_bunga" => 0,
                    "angsuran"       => 0,
                    "bunga"          => "0%",
                ];
            }else{
                $angsuranBunga = (($fxrate / 12) / 100) * $plafond;
                $angsuranPokok = $angsuranTotFix - $angsuranBunga;
                $angsuran      = round($angsuranBunga + $angsuranPokok);
                $angsuranFixed = $angsuran;
                $plafond -= (int)$angsuranPokok;
                
                $returnVal[$i] = [
                    "bulan"          => $i,
                    "sisa_pinjaman"  => $plafond,
                    "angsuran_pokok" => round($angsuranPokok),
                    "angsuran_bunga" => round($angsuranBunga),
                    "angsuran"       => $angsuran,
                    "bunga"          => $fxrate."%",
                ];
            }
        }

        $angsuranTotFloat = ((($flrate / 12) / 100) * $plafond) / (1 - (1 / pow((1.00 + (($flrate / 100) / 12.00)), ($fxflterm - $fxterm))));
        
        for($i = $fxterm + 1; $i < $n; $i++){
            if($i == $fxflterm){
                $angsuranBunga = (($flrate / 12) / 100) * $plafond;
                $angsuranPokok = $angsuranTotFloat - $angsuranBunga;

                $plafond -= $angsuranPokok;
                $angsuranPokok += $plafond;
                $angsuranBunga =  $angsuranTotFloat - $angsuranPokok;
                $angsuran      = round($angsuranBunga + $angsuranPokok);

                $returnVal[$i] = [
                    "bulan"          => $i,
                    "sisa_pinjaman"  => 0,
                    "angsuran_pokok" => round($angsuranPokok),
                    "angsuran_bunga" => round($angsuranBunga),
                    "angsuran"       => $angsuran,
                    "bunga"          => $flrate."%",
                ];

            }else{
                $angsuranBunga =  (($flrate / 12) / 100) * $plafond;
                $angsuranPokok =  $angsuranTotFloat - $angsuranBunga;

                $plafond       -= $angsuranPokok;
                $angsuran      =  round($angsuranBunga + $angsuranPokok);
                $angsuranFloat = $angsuran;

                $returnVal[$i] = [
                    "bulan"          => $i,
                    "sisa_pinjaman"  => round($plafond),
                    "angsuran_pokok" => round($angsuranPokok),
                    "angsuran_bunga" => round($angsuranBunga),
                    "angsuran"       => $angsuran,
                    "bunga"          => $flrate."%",
                ];
            }
        }

        $rincian = [
            "rincian" => [
                "plafond"             => $plafonds,
                "angsuranFixed"       => $angsuranFixed,
                "angsuranFloor"       => $angsuranFloor,
                "uang_muka"           => $downPayment,
                "suku_bunga"          => $fxrate."%",
                "suku_bunga_floating" => $flrate."%",
                "kredit_fix"          => $fxterm." Bulan",
                "lama_pinjaman"       => $fxflterm." Bulan",
            ],
            "angsuran_perbulan"  => $angsuran,
            "pembayaran_pertama" => $downPayment,
        ];

        return response()->success([
            'contents' => [
                'rincian_pinjaman' => $rincian,
                'detail_angsuran'  => $returnVal,
            ]
        ]);
    }

    public function generateEfektif_FixedFloorFloat()
    {
        $params  = $this->request->all();

        $price       = $params['price'];
        $fxflflterm  = $params['fxflflterm'];
        $ffxterm     = $params['ffxterm'];
        $fflterm     = $params['fflterm'];
        $ffxrate     = $params['ffxrate'];
        $ffloorrate  = $params['ffloorrate'];
        $ffloatlrate = $params['ffloatlrate'];
        $downPayment = $params['downPayment'];
        $total       = $price;

        $plafond     = $price - $downPayment;
        $plafonds    = $plafond;
        $n           = $fxflflterm + 1;
        $returnVal   = [];

        $angsuranTotFix = ((($ffxrate / 12) / 100) * $plafond) / (1 - (1 / pow(1.00 + (($ffxrate / 100) / 12.00), $fxflflterm)));
        for($i = 0; $i < $ffxterm + 1; $i++){
            if($i == 0){
                $returnVal[$i] = [
                    "bulan"          => $i,
                    "sisa_pinjaman"  => round($plafond),
                    "angsuran_pokok" => 0,
                    "angsuran_bunga" => 0,
                    "angsuran"       => 0,
                    "bunga"          => "0%",
                ];
            }else{
                $angsuranBunga = (($ffxrate / 12) / 100) * $plafond;
                $angsuranPokok = $angsuranTotFix - $angsuranBunga;
                $angsuran      = round($angsuranBunga + $angsuranPokok);
                $plafond      -= $angsuranPokok;
                $angsuranFixed = $angsuran;
                $returnVal[$i] = [
                    "bulan"          => $i,
                    "sisa_pinjaman"  => round($plafond),
                    "angsuran_pokok" => round($angsuranPokok),
                    "angsuran_bunga" => round($angsuranBunga),
                    "angsuran"       => $angsuran,
                    "bunga"          => $ffxrate."%",
                ];
            }
        }

        $angsuranTotFloor = ((($ffloorrate / 12) / 100) * $plafond) / (1 - (1 / pow((1.00 + (($ffloorrate / 100) / 12.00)), ($fxflflterm - $ffxterm))));

        for($i = $ffxterm + 1; $i < $fflterm + 1; $i++){
            $angsuranBunga = (($ffloorrate / 12) / 100) * $plafond;
            $angsuranPokok = $angsuranTotFloor - $angsuranBunga;

            $plafond -= $angsuranPokok;
            $angsuran      = round($angsuranBunga + $angsuranPokok);
            $angsuranFloor = $angsuran;
            $returnVal[$i] = [
                "bulan"          => $i,
                "sisa_pinjaman"  => round($plafond),
                "angsuran_pokok" => 0,
                "angsuran_bunga" => 0,
                "angsuran"       => $angsuran,
                "bunga"          => $ffloorrate."%",
            ];
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
                $angsuran      = round($angsuranBunga + $angsuranPokok);

                $returnVal[$i] = [
                    "bulan"          => $i,
                    "sisa_pinjaman"  => round($plafond),
                    "angsuran_pokok" => 0,
                    "angsuran_bunga" => 0,
                    "angsuran"       => $angsuran,
                    "bunga"          => $ffloatlrate."%",
                ];
            }else{
                $angsuranBunga = (($ffloatlrate / 12) / 100) * $plafond;
                $angsuranPokok = $angsuranTotFloat - $angsuranBunga;
                $plafond -= $angsuranPokok;
                $angsuran      = round($angsuranBunga + $angsuranPokok);
                $angsuranFloat = $angsuran;
                $returnVal[$i] = [
                    "bulan"          => $i,
                    "sisa_pinjaman"  => round($plafond),
                    "angsuran_pokok" => round($angsuranPokok),
                    "angsuran_bunga" => round($angsuranBunga),
                    "angsuran"       => $angsuran,
                    "bunga"          => $ffloatlrate."%",
                ];
            }
        }
        
        $rincian = [
            "rincian" => [
                "plafond"             => $plafonds,
                "angsuranFixed"       => round($angsuranFixed),
                "angsuranFloat"       => round($angsuranFloat),
                "angsuranFloor"       => round($angsuranFloor),
                "uang_muka"           => $downPayment,
                "suku_bunga"          => $ffxrate."%",
                "suku_bunga_floor"    => $ffloorrate."%",
                "suku_bunga_floating" => $ffloatlrate."%",
                "kredit_fix"          => $ffxterm." Bulan",
                "jangka_waktu_floor"  => $fflterm." Bulan",
                "lama_pinjaman"       => $fxflflterm." Bulan",
            ],
            "angsuran_perbulan"  => $angsuran,
            "pembayaran_pertama" => $downPayment,
        ];

        return response()->success([
            'contents' => [
                'rincian_pinjaman' => $rincian,
                'detail_angsuran'  => $returnVal,
            ]
        ]);
    }
}
