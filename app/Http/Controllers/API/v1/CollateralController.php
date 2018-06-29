<?php

namespace App\Http\Controllers\API\v1;

use DB;
use App\Models\Collateral;
use App\Models\OtsInArea;
use App\Models\OtsAccordingLetterLand;
use App\Models\OtsBuildingDesc;
use App\Models\OtsEnvironment;
use App\Models\OtsValuation;
use App\Models\OtsSeven;
use App\Models\OtsEight;
use App\Models\OtsNine;
use App\Models\OtsTen;
use App\Models\OtsAnotherData;
use App\Models\OtsPhoto;
use Illuminate\Http\Request;
use App\Models\UserServices;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Collateral\CreateOts;
use App\Http\Requests\API\v1\Collateral\CreateCollateral;
use App\Http\Requests\API\v1\Collateral\ChangeStatusRequest;
use App\Http\Requests\API\v1\Collateral\CreateOtsDoc;
use App\Models\Property;
use App\Models\EForm;
use App\Models\VisitReport;
use App\Models\Developer;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\CollateralDisposition;
use App\Notifications\CollateralOTS;
use App\Notifications\CollateralStafRejectOTS;
use App\Notifications\CollateralStafPenilaianAnggunan;
use App\Notifications\CollateralStafChecklist;
use App\Notifications\CollateralManagerRejected;
use App\Notifications\CollateralManagerRejectedAO;
use App\Notifications\CollateralManagerApprove;
use App\Notifications\CollateralManagerApproveAO;
use App\Notifications\CollateraAODisposition;
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
      $region = \RestwsHc::getRegion(intval($user['branch_id']));
      $developer_id = env('DEVELOPER_KEY',1);
      $data = $this->collateral->withAll()->where('developer_id','!=',$developer_id);
      if ($user['department'] != 'PJ. COLLATERAL MANAGER') {
        if ($user['role']!= 'superadmin') {
        $data->where('staff_id',(int)$this->request->header('pn'));
        }
      }
      else
      {
        $data->whereHas('property',function($property) use ($region)
        {
          $property->where('region_id',$region['region_id']);
        });
      }
      if ($this->request->has('status')) $data->where('status', $this->request->input('status'));
      $request = $this->request;
      if ($this->request->has('search'))
        $data->whereHas('property',function($property) use ($request)
        {
          $property->where(\DB::raw('LOWER(name)'),'ilike','%'.$request->input('search').'%');
        });
      if ($this->request->has('slug')) {
          $data = $this->collateral->withAll()->where('developer_id','=',$developer_id);
          $data->where('id',$this->request->input('slug'));
      }
      $data->orderBy('created_at', 'desc');
      return $this->makeResponse($data->paginate($this->request->has('limit') ? $this->request->limit : 10));
    }

    /**
     * Show Collateral Non Kerjasama
     * @return \Illuminate\Http\Response
     */
    public function indexNon()
    {
      $user = \RestwsHc::getUser();
      $region = \RestwsHc::getRegion(intval($user['branch_id']));
      $developer_id = env('DEVELOPER_KEY',1);
      $data = $this->collateral->GetLists($this->request)->where('developer_id','=',$developer_id);
      
      if ($user['department'] != 'PJ. COLLATERAL MANAGER') {
        if ($user['role']!= 'superadmin') {
            $data->where('staff_id',(int) $this->request->header('pn'));
        }
      }
      else
      {
        $data->where('region_id',$region['region_id']);
      }
      if ($this->request->has('slug')) {
          $data->where('collaterals_id',$this->request->input('slug'));
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

      $user = \RestwsHc::getUser();
      $data = [
        'developer_id' => $request->user()->id,
        'property_id' => $request->property_id,
        'remark' => $request->remark,
        'status' => Collateral::STATUS[0],
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

      $sendNotif = false;

      try {
        DB::beginTransaction();

        $developer_id = env('DEVELOPER_KEY',1);
        $collateral = $this->collateral->whereIn('status', [Collateral::STATUS[1],Collateral::STATUS[4]])->findOrFail($collateralId);
        OtsInArea::updateOrCreate(['collateral_id' => $collateral->id],['collateral_id' => $collateral->id]+$this->request->area);
        OtsAccordingLetterLand::updateOrCreate(['collateral_id' => $collateral->id],['collateral_id' => $collateral->id]+$this->request->letter);
        OtsBuildingDesc::updateOrCreate(['collateral_id' => $collateral->id],['collateral_id' => $collateral->id]+$this->request->building);
        OtsEnvironment::updateOrCreate(['collateral_id' => $collateral->id],['collateral_id' => $collateral->id]+$this->request->environment);
        OtsValuation::updateOrCreate(['collateral_id' => $collateral->id],['collateral_id' => $collateral->id]+$this->request->valuation);
        OtsSeven::updateOrCreate(['collateral_id' => $collateral->id],['collateral_id' => $collateral->id]+$this->request->seven);
        OtsEight::updateOrCreate(['collateral_id' => $collateral->id],['collateral_id' => $collateral->id]+$this->request->eight);
        OtsNine::updateOrCreate(['collateral_id' => $collateral->id],['collateral_id' => $collateral->id]+$this->request->nine);
        OtsTen::updateOrCreate(['collateral_id' => $collateral->id],['collateral_id' => $collateral->id]+$this->request->ten);
        $otsOther = OtsAnotherData::updateOrCreate(['collateral_id' => $collateral->id],['collateral_id' => $collateral->id]+$dataother);
        if (count($dataother['image_area'])>0) {
          $photo_id = array();
          foreach ($dataother['image_area'] as $key => $value) {
            $photos = OtsPhoto::create(['ots_other_id'=>$otsOther->id]+$value);
            $photo_id[]=$photos->id;
          }
          OtsPhoto::where('ots_other_id', $otsOther->id)->whereNotIn('id', $photo_id)->delete();
          if ($collateral->developer_id == $developer_id) {
          $collateralView = DB::table('collateral_view_table')->where('collaterals_id', $collateralId)->first();
          if ( count($collateralView) > 0 ) {
            $eform = EForm::find($collateralView->eform_id);
            if ( count($eform) > 0 ) {
              foreach( $otsOther->images as $image ) {
                $paths = explode('/', $image->image_data);
                $filename = $paths[ count($paths) - 1 ];
                copy(
                  public_path( 'uploads/collateral/other/' . $otsOther->id . '/' . $filename )
                  , public_path( 'uploads/' . $eform->nik . '/' . $filename )
                );
                }
              }
            }
          }
        }
        $collateral->status = Collateral::STATUS[2];
        $collateral->save();
        $sendNotif = true;
        DB::commit();

      } catch (Exception $e) {
        DB::rollback();
        \Log::info($e);

      }

      if ( $sendNotif ){
        if(env('PUSH_NOTIFICATION', false)){
            //cek notif collateral
            $aksiCollateral = 'collateral_penilaian_ots';
            $cekNotifColltaeralOTS=[];
            if(empty($cekNotifColltaeralOTS))
            {
              \Log::info('=======notification web and mobile sent to manager collateral  ======');
              $collateral_id =$collateralId;
              $dataCollateral = Collateral::find($collateralId);
              if(!empty($dataCollateral->manager_id))
              {
                  $manager_id = $dataCollateral->manager_id; //id manager collateral
                  //insert data from notifications table
                  $getDataEform  = DB::table('collateral_view_table')->where('collaterals_id', $collateralId)->first();
                  if($getDataEform){
                    $eform_id = $getDataEform->eform_id;
                    $eform = Eform::with('customer')->where('id',$eform_id)->first();
                    $user_id = $eform->user_id;
                    $user_login = \RestwsHc::getUser();
                    $msgData = [
                      'customer_name' => $eform['customer']['first_name'].' '.$eform['customer']['last_name'],
                      'user_login' => $user_login['name'],
                      'ref_number' => $eform->ref_number
                    ];
                    $message = getMessage('collateral_penilaian', $msgData);
                  }else{
                    $user_id = $collateral->developer_id;
                    $message = getMessage('collateral_penilaian');
                  }
                  $usersModel = User::where('id',$user_id)->first();
                  $dataUser  = UserServices::where('pn',$manager_id)->first();
                  $branch_id = $dataUser['branch_id'];
                  $usersModel->notify(new CollateralStafPenilaianAnggunan($dataCollateral,$branch_id));
                  $userNotif = new UserNotification;
                  // Get data from notifications table
                  $notificationData = $userNotif->where('slug', $collateralId)->where('type_module',$aksiCollateral)
                                                 ->orderBy('created_at', 'desc')->first();
                  $id = $notificationData['id'];
                  $message = getMessage('collateral_penilaian',  $msgData);
                  $credentials = [
                     'headerNotif' => $message['title'],
                     'bodyNotif' => $message['body'],
                     'id' => $id,
                     'type' => 'collateral_penilaian_agunan',
                     'slug' => $collateral_id,
                     'user_id' => $manager_id,
                     'receiver' => 'manager_collateral',
                  ];
                  pushNotification($credentials,'general');
              }
            }else{
              \Log::info('======= Tidak kirim notification web and mobile karena sudah ada  ======');
            }
        }
      }

      return $this->makeResponse(
        $this->collateral->withAll()->find($collateralId)
      );
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
      $collateral = $this->collateral->whereIn('status', [Collateral::STATUS[1], Collateral::STATUS[2], Collateral::STATUS[4]])->findOrFail($collateralId);
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
        if ( $request->has('eform_id') && $request->eform_id != 'false' ) {
            $eformdata = EForm::findOrFail($request->input('eform_id'));
            $hasapprove = $eformdata->is_approved;
        }
        else
        {
          $hasapprove = false;
        }
          if ($collateral->developer_id == $developer_id && $hasapprove) {
              $sentclas =  EForm::approve( $eformdata->id, $eformdata );
               if ($sentclas['status']) {
               \DB::commit();
               }else
               {
               \DB::rollback();
               }
              $eform = $eformdata;
              generate_pdf('uploads/'. $eform->nik, 'collateral.pdf', view('pdf.collateral', compact('eform','collateral')));
          }
          else
          {
            \DB::commit();
          }
      }
      if ($action === 'reject') {
        $collateral->remark = $this->request->remark;
        $collateral->save();
        \DB::commit();
      }
      if(env('PUSH_NOTIFICATION', false))
       {
            \Log::info('=======notification web approve or reject collateral  ======');
            $pn = $this->request->header('pn');
            $collateral = Collateral::where('id',$collateralId)->first();
            $developer_id =  $collateral->developer_id;
            $user_id =  $developer_id;
            $usersModel = User::find($user_id);
            $dataUser  = UserServices::where('pn', $pn)->first();
            $branch_id = $dataUser['branch_id'];
            $pushNotif = true;
            if ($action === 'approve')
            {
              if($developer_id != 1)
              {
                // Notif Akan Dikirim ke Admin Developer
                $bodyNotif = 'approval collateral';
                $status = 'collateral_approve';
                $type = 'collateral_manager_approving';
                $receiver = 'staf_collateral';
                $user_id = $collateral->staff_id;
                //insert data from notifications table
                $usersModel->notify(new CollateralManagerApprove($collateral,$branch_id));
                $userNotif = new UserNotification;
                // Get data from notifications table
                $notificationData = $userNotif->where('slug', $collateralId)->where('type_module',$type)
                ->orderBy('created_at', 'desc')->first();
              }

                // Notif Akan Dikirim ke AO
                $bodyNotif = 'approval collateral';
                $status = 'collateral_approve';
                $type = 'collateral_manager_approving';
                $receiver = 'staf_collateral';
                $user_id = $collateral->staff_id;
                //insert data from notifications table
                $usersModel->notify(new CollateralManagerApproveAO($collateral,$branch_id));
                $userNotif = new UserNotification;
                // Get data from notifications table
                $notificationData = $userNotif->where('slug', $collateralId)->where('type_module',$type)
                ->orderBy('created_at', 'desc')->first();

            }
            else if ($action === 'reject')
            {
                $role = $dataUser['role'];
                if ($role=='collateral')  //reject penilaian anggunan untuk developer
                {
                  if($developer_id != 1)
                  {
                    // Notif Akan Dikirim ke Admin Developer
                    $bodyNotif = 'reject collateral';
                    $status    = 'collateral_reject';
                    $type = 'collateral_'.$action;
                    $receiver = 'staf_collateral';
                    $user_id = $collateral->staff_id;
                    //insert data from notifications table
                    $usersModel->notify(new CollateralManagerRejected($collateral,$branch_id));
                    $userNotif = new UserNotification;
                    // Get data from notifications table
                    $notificationData = $userNotif->where('slug', $collateralId)->where('type_module','collateral_manager_approving')->orderBy('created_at', 'desc')->first();
                  }

                    // Notif Akan Dikirim ke AO
                    $bodyNotif = 'reject collateral';
                    $status    = 'collateral_reject';
                    $type = 'collateral_'.$action;
                    $receiver = 'staf_collateral';
                    $user_id = $collateral->staff_id;
                    //insert data from notifications table
                    $usersModel->notify(new CollateralManagerRejectedAO($collateral,$branch_id));
                    $userNotif = new UserNotification;
                    // Get data from notifications table
                    $notificationData = $userNotif->where('slug', $collateralId)->where('type_module','collateral_manager_approving')->orderBy('created_at', 'desc')->first();

                }
                else  //reject untuk staf collateral dan ao
                {
                   $bodyNotif = 'menolak menilai agunan';
                   $status = 'collateral_reject_penilaian';
                   $type = 'collateral_ots_'.$action;
                   $receiver = 'manager_collateral';
                   if(!empty($collateral['manager_id']))
                   {
                    $user_id  = $collateral['manager_id'];
                    //insert data from notifications table
                    $getDataEform  = DB::table('collateral_view_table')->where('collaterals_id', $collateralId)->first();
                      if($getDataEform){
                        $eform_id = $getDataEform->eform_id;
                        $eform = Eform::where('id',$eform_id)->first();
                        $id_nasabah = $eform->user_id;
                      }else{
                        $id_nasabah = $developer_id;
                      }
                    $usersModel = User::where('id',$id_nasabah)->first();
                    $usersModel->notify(new CollateralStafRejectOTS($collateral,$branch_id));

                     $userNotif = new UserNotification;
                    // Get data from notifications table
                     $notificationData = $userNotif->where('slug', $collateralId)->where('type_module','collateral')
                                                   ->orderBy('created_at', 'desc')->first();
                   }
                   else
                   {
                     $user_id = 'kosong';
                   }
                }
            }
            if($user_id !='kosong' && $pushNotif == true)
            {
              if($status == 'collateral_approve'){
                if($request->has('eform_id')){
                  $user_login = \RestwsHc::getUser();
                  $eform   = Eform::with('customer')->where('id', $request->input('eform_id'))->first();
                  $msgData = [
                    'customer_name' => $eform['customer']['first_name'].' '.$eform['customer']['last_name'],
                    'ref_number'    => $eform->ref_number,
                    'user_login'    => $user_login['name']
                  ];
                  $message = getMessage($status, $msgData);
                }else{
                  $message = getMessage($status);  
                }
              }else{
                $message = getMessage($status);
              }
              $id = $notificationData['id'];
              $credentials = [
                  'headerNotif' => 'Collateral Notification',
                  'bodyNotif' => $message['body'],
                  'id' => $id,
                  'type' => $type,
                  'slug' => $collateralId,
                  'user_id' => $user_id,
                  'receiver' => $receiver,
                ];
              pushNotification($credentials,'general');
            }
          }
        //end Web notif

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

      $baseRequest = $this->request->only('staff_id', 'staff_name', 'status', 'remark','is_staff');

      $user = \RestwsHc::getUser();
      $baseRequest['manager_id'] = $user['pn'];
      $baseRequest['manager_name'] = $user['name'];
      $baseRequest['dispose_by'] = $user['pn'];

      $this->collateral->where( 'status', Collateral::STATUS[0] )
        ->findOrFail( $collateralId )
          ->update( $baseRequest );
      if(env('PUSH_NOTIFICATION', false))
       {
        \Log::info('=======notif disposisi ke staff colleteral atau ao ======');
        $dataInput = $this->request->all();
        $staff_id = $dataInput['staff_id'];
        //insert data from notifications table collateral disposition
        $type = 'collateral_disposition';
        $dataCollateral = Collateral::where('id',$collateralId)->first();
        $developer_id = $dataCollateral->developer_id;
        $getDataEform  = DB::table('collateral_view_table')->where('collaterals_id', $collateralId)->first();  //check data eform
        if($getDataEform)
        {
          $eform_id = $getDataEform->eform_id;
          $eform = Eform::with('customer')->where('id',$eform_id)->first();
          $user_id = $eform->user_id;
          $user_login = \RestwsHc::getUser();
          $msgData = [
            'customer_name' => $eform['customer']['first_name'].' '.$eform['customer']['last_name'],
            'ref_number' => $eform->ref_number
          ];
          $message = getMessage('collateral_disposition', $msgData);
        }else
        {
          $user_id= $developer_id;
          $message = getMessage('collateral_disposition');
        }

        $usersModel = User::where('id',$user_id)->first();
        $dataUser  = UserServices::where('pn',$staff_id)->first();
        $branch_id = $dataUser['branch_id'];
        if($dataUser['role'] == 'ao')
        {
          $usersModel->notify(new CollateraAODisposition($dataCollateral,$branch_id));
          $receiver ='ao';
        }else
        {
          $usersModel->notify(new CollateralDisposition($dataCollateral,$branch_id));
          $receiver ='staf_collateral';
        }
        $userNotif = new UserNotification;
        $notificationData = $userNotif->where('slug', $collateralId)->where('type_module','collateral')
                                        ->orderBy('created_at', 'desc')->first();
        $id = $notificationData['id'];
        // $message = getMessage('collateral_disposition');
        $credentials = [
            'slug' => $collateralId,
            'id' => $id,
            'user_id' => $staff_id,
            'headerNotif' => 'Collateral Notification',
            'bodyNotif' => $message['body'],
            'type' => 'collateral_disposition',
            'receiver' => $receiver,
        ];
        // Call the helper of push notification function
        pushNotification($credentials,'general');
       }
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

            $manager_id= $store['manager_id'];
             if(env('PUSH_NOTIFICATION', false))
            {
                if(!empty($manager_id))
                {
                    //insert data from notifications table
                    $dataCollateral = Collateral::where('id',$collateralId)->first();
                    $getDataEform  = DB::table('collateral_view_table')->where('collaterals_id', $collateralId)->first();
                    if($getDataEform){
                      $eform_id = $getDataEform->eform_id;
                      $eform = Eform::where('id',$eform_id)->first();
                      $user_id = $eform->user_id;
                    }else{
                      $user_id = $dataCollateral->developer_id;
                    }
                    $usersModel = User::where('id',$user_id)->first();
                    $dataUser  = UserServices::where('pn',$manager_id)->first();
                    $branch_id = $dataUser['branch_id'];
                    $usersModel->notify(new CollateralStafChecklist($dataCollateral,$branch_id));
                    $userNotif = new UserNotification;
                    // Get data from notifications table
                    $notificationData = $userNotif->where('slug', $collateralId)
                                                    ->orderBy('created_at', 'desc')->first();
                    $id = $notificationData['id'];
                    $message = getMessage('collateral_checklist');
                     $credentials = [
                      'headerNotif' => $message['title'],
                      'bodyNotif' => $message['body'],
                      'id' => $id,
                      'type' => 'collateral_checklist',
                      'slug' => $collateralId,
                      'user_id' => $manager_id,
                      'receiver' => 'manager_collateral',
                      ];
                     pushNotification($credentials,'general');
                }
            }

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

     public function sendNotifOTS($collateral_id,$typeKpr){
        $dataCollateral = Collateral::find($collateral_id);
        $id_manager_collateral = $dataCollateral->manager_id;
        $user_login = \RestwsHc::getUser();
        //*
        //insert data from notifications table
        $type = 'collateral_ots';
        $getDataEform  = DB::table('collateral_view_table')->where('collaterals_id', $collateral_id)->first();
        if($getDataEform){
          $eform_id = $getDataEform->eform_id;
          $eform = Eform::with('customer')->where('id',$eform_id)->first();
          $user_id = $eform->user_id;
          $msgData = [
            'customer_name' => $eform['customer']['first_name'].' '.$eform['customer']['last_name'],
            'user_login' => $user_login['name']
          ];
          $message = getMessage('collateral_ots', $msgData);
        }
        else
        {
          $user_id =$dataCollateral->developer_id;
          $message = getMessage('collateral_ots');
        }
        $usersModel = User::where('id',$user_id)->first();
        $dataUser  = UserServices::where('pn',$id_manager_collateral)->first();
        $branch_id = $dataUser['branch_id'];
        $userNotif = new UserNotification;
        $usersModel->notify(new CollateralOTS($dataCollateral,$branch_id));
        // Get data from notifications table
        $notificationData = $userNotif->where('slug', $collateral_id)->where('type_module','collateral')
                                        ->orderBy('created_at', 'desc')->first();
        $id = $notificationData['id'];
        // $message = getMessage('collateral_ots');
        //*/
        $credentials = [
            'headerNotif' => $message['title'],
            'bodyNotif' => $message['body'],
            'id' => $id,
            'type' => 'collateral_ots',
            'slug' => $collateral_id,
            'user_id' => $id_manager_collateral,
            'receiver' => 'manager_collateral',
        ];
        if(!empty($id_manager_collateral))
        {
           pushNotification($credentials,'general');
        }
    }

      /**
     * Show detail Notif mobiel ots non index
     * @param  string $type
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function NotifOtsNonindex($type, $developerId, $propertyId)
    {
      $ots =  $this->collateral->withAll()->where('developer_id', $developerId)->where('property_id', $propertyId)->firstOrFail()->toArray();
      $nonkerjasama = $this->collateral->GetDetails($developerId, $propertyId)->firstOrFail()->toArray();
      $visitreport = VisitReport::where('eform_id',$nonkerjasama['eform_id'])->firstOrFail()->toArray();
      unset($visitreport['id']);
      $data = array_merge($ots,$nonkerjasama,$visitreport);

       if(env('PUSH_NOTIFICATION', false))
       {
      // send notification to mobile
        $colleteral_id = $nonkerjasama['collaterals_id'];
        $typeKpr = 'Non Kerja Sama';
        $this->sendNotifOTS($colleteral_id,$typeKpr);
      //end notification
       }

      return $this->makeResponse(
        $data
      );
    }


     /**
     * Show detail Notif mobiel ots  index
     * @param  string $type
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function NotifOts($type, $developerId, $propertyId)
    {
      // send notification to mobile
      $collateral = $this->collateral->withAll()->where('developer_id', $developerId)->where('property_id', $propertyId)->firstOrFail();

       $collateral_id= $collateral->id;
       $typeKpr = 'Developer';
       if(env('PUSH_NOTIFICATION', false))
       {
        $this->sendNotifOTS($collateral_id,$typeKpr);
       }
      //end notification

      return $this->makeResponse(
        $this->collateral->withAll()->where('developer_id', $developerId)->where('property_id', $propertyId)->firstOrFail()
      );

    }

    /*
    *  Show data notif
    */
    public function notifCollateral($type ,$collateralId){
     $collateral = DB::table('collateral_view_table')->where('collaterals_id', $collateralId)->first();
     if($collateral)
     {
        $developer_id = $collateral->developer_id;
        if($developer_id !=1)
        {
          $collateral = $this->collateral->withAll()->where('id','=',$collateralId)->first();
          $developer_id = $collateral['property']['developer_id'];
        }
     }else{
        $collateral = $this->collateral->withAll()->where('id','=',$collateralId)->first();
        $developer_id = $collateral['property']['developer_id'];
     }
     return response()->success( [
        'contents' => $collateral
     ] );
    }

    /*
    *  Get Id collateral from property_id
    */
    public function getIdCollateral($type ,$property_id){
     $collateral = Collateral::where('property_id', $property_id)->first();
     return response()->success( [
        'contents' => $collateral
     ] );
    }

    public function GetAll(Request $request, $type = null)
    {
      $developer_id = env('DEVELOPER_KEY',1);
      $limit = $request->has('limit') ? $request->input('limit') : 10;
      $temp = [];
      if ($type == 'nonindex') {
          $non = $this->collateral->GetLists($this->request)->where('developer_id',$developer_id)->limit($limit)->get()->toArray();
          foreach ($non as $key => $value) {
              $ots = $this->collateral->withAll()->FindOrFail($value['collaterals_id'])->toArray();
              $visitreport = VisitReport::where('eform_id',$value['eform_id'])->first()->toArray();
              unset($visitreport['id']);
              $all = array_merge($ots,$value,$visitreport);
              $temp[]= $all;
            }
          $page = $request->input('page', 1);
          $perPage = $limit;
          $offset = ($page * $perPage) - $perPage;
          $data =  new \Illuminate\Pagination\LengthAwarePaginator(
          array_slice($temp, $offset, $perPage, true),
          count($temp),
          $perPage,
          $page,
          ['path' => $request->url(), 'query' => $request->query()]
        );
        }else if ($type == 'index')
        {
          $data = $this->collateral->withAll()->where('developer_id','!=',$developer_id)->paginate($limit);
        }
        else
        {
          return response()->error( [
          'message' => 'Type Collateral Tidak Valid'
          ],422 );
        }
      return response()->success( [
        'contents' => $data
        ] );
    }

}
