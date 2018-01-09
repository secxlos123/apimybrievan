<?php

namespace App\Http\Controllers\API\v1;

use DB;
use App\Models\OtsInArea;
use App\Models\Collateral;
use App\Models\OtsValuation;
use Illuminate\Http\Request;
use App\Models\OtsEnvironment;
use App\Models\OtsAnotherData;
use App\Models\OtsBuildingDesc;
use App\Http\Controllers\Controller;
use App\Models\OtsOtsAccordingLetterLand;
use App\Http\Requests\API\v1\Collateral\CreateOts;
use App\Http\Requests\API\v1\Collateral\CreateCollateral;
use App\Http\Requests\API\v1\Collateral\ChangeStatusRequest;
use App\Http\Requests\API\v1\Collateral\CreateOtsDoc;
use App\Models\Property;
use App\Models\EForm;
use App\Models\VisitReport;

class CollateralController extends Controller
{
    /**
     * Collateral instance
     * @var \App\Models\Collateral
     */
    protected $collateral;

    /**
     * Request instance
     * @var Request
     */
    protected $request;

    /**
     * Initialize instance
     * @param Collateral $collateral
     * @param Request    $request
     */
    public function __construct(Collateral $collateral, Request $request)
    {
      $this->collateral = $collateral;
      $this->request = $request;
    }

    /**
     * Show collateral list
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $user = \RestwsHc::getUser();
      \Log::info($user);
      $developer_id = env('DEVELOPER_KEY',1);
      $data = $this->collateral->withAll()->where('developer_id','!=',$developer_id)->orderBy('created_at', 'desc');
      if ($user['department'] != 'PJ. COLLATERAL MANAGER') {
        $data->where('staff_id',(int)$this->request->header('pn'));
      }
      return $this->makeResponse($data->paginate($this->request->has('limit') ? $this->request->limit : 10));
    }

    /**
     * Show Collateral Non Kerjasama
     * @return \Illuminate\Http\Response
     */
    public function indexNon()
    {
      $user = \RestwsHc::getUser();
      \Log::info($user);
      $developer_id = env('DEVELOPER_KEY',1);
      $data = $this->collateral->GetLists($this->request)->where('developer_id','=',$developer_id)->where('is_approved',true);
      if ($user['department'] != 'PJ. COLLATERAL MANAGER') {
        $data->where('staff_id',(int) $this->request->header('pn'));
      }
      return $this->makeResponse($data->paginate($this->request->has('limit') ? $this->request->limit : 10));
    }

    /**
     * Show detail collateral
     * @param  string $type
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($type, $developerId, $propertyId)
    {
      return $this->makeResponse(
        $this->collateral->withAll()->where('developer_id', $developerId)->where('property_id', $propertyId)->firstOrFail()
      );
    }

    /**
     * Show detail collateral Non Kerja
     * @param  string $type
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function showNon($type, $developerId, $propertyId)
    {
      $ots =  $this->collateral->withAll()->where('developer_id', $developerId)->where('property_id', $propertyId)->firstOrFail()->toArray();
      $nonkerjasama = $this->collateral->GetDetails($developerId, $propertyId)->firstOrFail()->toArray();
      $visitreport = VisitReport::where('eform_id',$nonkerjasama['eform_id'])->firstOrFail()->toArray();
      unset($visitreport['id']);
      $data = array_merge($ots,$nonkerjasama,$visitreport);
      return $this->makeResponse(
        $data
      );
    }

    /**
     * Store new collateral
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCollateral $request, $eks)
    {
      if ($eks !== 'eks') return response()->error([
        'message' => 'Tidak Bisa Membuat collateral jika dalam internal'
      ]);

      $data = [
        'developer_id' => $request->user()->id,
        'property_id' => $request->property_id,
        'remark' => $request->remark,
        'status' => Collateral::STATUS[0]
      ];
      $collateral = $this->collateral->create($data);
      return $this->makeResponse(
        $this->collateral->withAll()->findOrFail($collateral->id)
      );
    }

    /**
     * Update collateral
     * @return \Illuminate\Http\Response
     */
    public function update($eks, $id)
    {
      return $this->makeResponse(
        $this
          ->collateral
          ->where('status', Collateral::STATUS[0])
          ->findOrFail($id)
          ->update($this->request->only(['status', 'approved_by', 'staff_id', 'staff_name','is_staff']))
          ? $this->collateral->findOrFail($id)
          : (object)[]
      );
    }

    /**
     * Store new ots collateral
     * @param  CreateOts $request
     * @param  string    $eks
     * @param  integer   $collateralId
     * @return \Illuminate\Http\Response
     */
    public function storeOts(CreateOts $request, $eks, $collateralId)
    {
      if (!array_key_exists('image_area',$this->request->other)) {
        $imagearea = array();
        $dataother = array_merge($this->request->other,['image_area' => $imagearea]);
      }else
      {
        $dataother = $this->request->other;
      }
      return DB::transaction(function() use($collateralId) {
        $collateral = $this->collateral->where('status', Collateral::STATUS[1])->findOrFail($collateralId);
        $collateral->otsInArea()->create($this->request->area);
        $collateral->otsLetter()->create($this->request->letter);
        $collateral->otsBuilding()->create($this->request->building);
        $collateral->otsEnvironment()->create($this->request->environment);
        $collateral->otsValuation()->create($this->request->valuation);
        $collateral->otsSeven()->create($this->request->seven);
        $collateral->otsEight()->create($this->request->eight);
        $collateral->otsNine()->create($this->request->nine);
        $collateral->otsTen()->create($this->request->ten);
        $otsOther = $collateral->otsOther()->create($dataother);
        $otsOther->image_condition_area = $this->uploadAndGetFileNameImage($otsOther);
        $otsOther->save();
        $collateral->status = Collateral::STATUS[2];
        $collateral->save();
        return $this->makeResponse(
          $this->collateral->withAll()->find($collateralId)
        );
      });
    }

    /**
     * Get ots collateral
     * @param  string $eks
     * @param  integer $collateralId
     * @return \Illuminate\Http\Response
     */
    public function getOts($eks, $collateralId)
    {
      return $this->makeResponse(
        $this->collateral->withAll()->findOrFail($collateralId)
      );
    }

    /**
     * Change status
     * @param  string $eks
     * @param  string $action
     * @param  integer $collateralId
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(ChangeStatusRequest $request, $eks, $action, $collateralId)
    {
      \DB::beginTransaction();
      $developer_id = env('DEVELOPER_KEY',1);
      $collateral = $this->collateral->whereIn('status', [Collateral::STATUS[1], Collateral::STATUS[2]])->findOrFail($collateralId);
      $property = Property::findOrFail($collateral->property_id);
      $prevStatus = $collateral->status;
      $handleReject = function($prevStatus) {
        return $prevStatus === Collateral::STATUS[1] ? Collateral::STATUS[0] : Collateral::STATUS[4];
      };
      $collateral->status = $action === 'approve' ? Collateral::STATUS[3] : $handleReject($prevStatus);
      if ($action === 'approve') {
        $collateral->remark = $this->request->remark;
        $collateral->approved_by = $this->request->header('pn');
        $property->is_approved = true;
        $collateral->save();
        $property->save();
          if ($collateral->developer_id == $developer_id ) {
              $eformdata = EForm::findOrFail($request->input('eform_id'));
              $sentclas =  EForm::approve( $eformdata->id, $eformdata );
              if ($sentclas['status']) {
               \DB::commit();
              }else
              {
              \DB::rollback();
              }
          }
          else
          {
            $property->save();
            $collateral->save();
            \DB::commit();
          }
      }
      if ($action === 'reject') {
        $collateral->remark = $this->request->remark;
        $collateral->save();
        \DB::commit();
      }
      return $this->makeResponse(
        $this->collateral->withAll()->findOrFail($collateralId)
      );
    }

    /**
     * Disposition change staff name, staff id
     * @param  string $eks
     * @param  integer $collateralId
     * @return \Illuminate\Http\Response
     */
    public function disposition($eks, $collateralId)
    {
      $this->request->request->add(['status' => Collateral::STATUS[1]]);
      $this->collateral->where('status', Collateral::STATUS[0])->findOrFail($collateralId)->update($this->request->only('staff_id', 'staff_name', 'status', 'remark','is_staff'));
      return $this->makeResponse(
        $this->collateral->withAll()->findOrFail($collateralId)
      );
    }

    /**
     *
     * @param  string $otsOther
     * @param  integer $collateralId
     * @return \Illuminate\Http\Response
     */
    private function uploadAndGetFileNameImage($otsOther)
    {
      $image = $this->request->file('other.image_condition_area');
      $filename = $otsOther->id . '-' . time() . '.' . $image->extension();
      $path = 'collateral/ots/other';
      $image->storeAs($path, $filename);
      return url('/') . '/uploads/' . $path . '/' . $filename;
    }

    /**
     * Build response json
     * @param  mixed $data
     * @return \Illuminate\Http\Response
     */
    private function makeResponse($data)
    {
      return response()->success([
        'contents' => $data
      ]);
    }

    /**
     * Public Function Upload Ots_Document
     * @param  string $eks
     * @param  integer $collateralId
     * @return \Illuminate\Http\Response
     */
    public function storeOtsDoc(CreateOtsDoc $request, $eks, $collateralId)
    {
      \DB::beginTransaction();
      try {
            $store = $this->collateral->findOrFail($collateralId);
            $data = $store->otsDoc()->updateOrCreate(['collateral_id'=>$collateralId],$request->all());
            \DB::commit();
        return response()->success([
                'message' => 'Data Collateral berhasil ditambah.',
                'contents' => $data,
            ], 201);
      } catch (Exception $e) {
        \DB::rollback();
        return response()->error([
                'message' => 'Data Collateral Gagal ditambah.',
                'contents' => $e,
            ], 422);
      }
    }

     /**
     * Public Function show Ots_Document
     * @param  string $eks
     * @param  integer $collateralId
     * @return \Illuminate\Http\Response
     */
    public function showOtsDoc($eks, $collateralId)
    {
      return $this->makeResponse(
        $this->collateral->with('otsDoc')->findOrFail($collateralId)
      );
    }
}
