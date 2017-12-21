<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Models\ApprovalDataChange;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\ApprovalDataChange\CreateRequest;
use App\Models\User;

class ApprovalDataChangeController extends Controller
{

    /**
     * Approval instance
     * @var ApprovalDataChange
     */
    protected $approvalDateChange;

    /**
     * Request instance
     * @var Request
     */
    protected $request;

    /**
     * Init instance
     * @param ApprovalDataChange $approvalDateChange
     * @param Request $request
     */
    public function __construct(ApprovalDataChange $approvalDateChange, Request $request)
    {
      $this->approvalDateChange = $approvalDateChange;
      $this->request = $request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type, $approvalType)
    {
        return $this->makeResponse(
          $this->approvalDateChange->getList()->only($approvalType)->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request, $type, $userType)
    {
        if ($this->validRole()) {
          $this->mapRequest($userType);
          $approvalDataChange = $this->approvalDateChange->create(
            $this->request->only(
              $this->approvalDateChange->getFillable()
            )
          );
          if ($this->request->hasFile('logo')) {
            $approvalDataChange->logo = $this->uploadAndGetFileNameImage($approvalDataChange);
          }
          $approvalDataChange->save();
          return $this->makeResponse(
            $approvalDataChange ? $this->approvalDateChange->getList()->findOrFail($approvalDataChange->id) : null
          );
        }
        return response()->error([
          'message' => 'Access Danied'
        ]);
    }

    /**
     * for upload image logo
     * @param  integer $collateralId
     * @return \Illuminate\Http\Response
     */
    private function uploadAndGetFileNameImage($approvalDataChange)
    {
        $image = $this->request->file('logo');
        $filename = $approvalDataChange->id . '-' . time() . '.' . $image->extension();
        $path = 'approval-data-change';
        $image->storeAs($path, $filename);
        return url('/') . '/uploads/' . $path . '/' . $filename;
    }

    /**
     * Check role user
     * @return boolean
     */
    public function validRole()
    {
      $user = $this->request->user();
      return $user->inRole('developer') || $user->inRole('others');
    }

    /**
     * add request http input
     * @param  string $type
     * @return void
     */
    public function mapRequest($type)
    {
      $user = $this->request->user();
      $user = $user->inRole('developer') ? $user->developer : $user->thirdparty;
      $this->request->request->add([
        $this->approvalDateChange->getFillable()[0]  => $user->id,
        $this->approvalDateChange->getFillable()[1]  => $this->resolveType($type)
      ]);
    }

    /**
     * Resolve type related type
     * @param  string $target
     * @return string
     */
    public function resolveType($target)
    {
      $types = [
        'developer' => \App\Models\Developer::class,
        'third-party' => \App\Models\ThirdParty::class
      ];
      return !empty($types[$target]) ? $types[$target] : 'Unknown';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($eks, $approvalType, $id)
    {
      $new = $this->approvalDateChange->getList($id)->only($approvalType)->findOrFail($id);
      $old = User::with($approvalType.'.city')->findOrFail($new->related_id);
      $newold = compact('new','old');
      return $this->makeResponse(
        $newold
      );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
      return $this->makeResponse(
        $this->approvalDateChange->findOrFail($id)->update(
          $this->request->only(
            $this->approvalDateChange->getFillable()
          )
        )
      );
    }

    /**
     * Change status approval data change
     * @param  string $type
     * @param  string $status
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($type, $approvalType, $status, $id)
    {
      return $this->{$status}($approvalType, $id);
    }

    /**
     * Approve data change
     * @param  Request $request
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($approvalType, $id)
    {
      $this->changeData($approvalType, $id);
      return $this->makeResponse(
        $this->approvalDateChange->approve($id, $approvalType, $this->request->header('pn'))
      );
    }

    private function changeData($approvalType, $id)
    {
      $dataChange = $this->approvalDateChange->only($approvalType)->findOrFail($id);
      $related = $dataChange->related;

      if (!$related) {
        throw new \Illuminate\Database\Eloquent\ModelNotFoundException;
      }

      if ($dataChange->company_name) {
        if ($dataChange->isDeveloper()) {
          $related->company_name = $dataChange->company_name;
        } else {
          $related->name = $dataChange->company_name;
        }
      }

      if ($dataChange->summary && $dataChange->isDeveloper()) {
        $related->summary = $dataChange->summary;
      }

      if ($dataChange->phone) {
        if ($dataChange->isThirdParty()) {
          $related->phone_number = $dataChange->phone;
        }
        $related->user->phone = $dataChange->phone;
      }

      if ($dataChange->mobile_phone) {
        $related->user->mobile_phone = $dataChange->mobile_phone;
      }

      if ($dataChange->city_id) {
        $related->city_id = $dataChange->city_id;
      }
       if ($dataChange->address) {
        $related->address = $dataChange->address;
      }

      if ($dataChange->logo) {
        $path = $this->transformToArray($dataChange);
        $path = $this->removeFirstAndSecondArrayPath($path);
        $newImage = $this->createImageName($path);
        $this->copyToAvatar($path, $newImage);
        \DB::table($related->user->getTable())->update(['image' => $newImage]);
      }

      $related->user->save();
      $related->save();
    }

    private function removeFirstAndSecondArrayPath($path)
    {
      array_shift($path);
      array_shift($path);
      return $path;
    }

    private function createImageName($path)
    {
      return time()  . '.' . explode('.', $path[1])[1];
    }

    private function transformToArray($dataChange)
    {
      return explode('/', preg_replace('(^https?://)', '', $dataChange->logo));
    }

    private function copyToAvatar($path, $newImage)
    {
      \Storage::copy(implode('/', $path), 'avatars/' . $newImage);
    }

    /**
     * Reject data change
     * @param  Request $request
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function reject($approvalType, $id)
    {
      return $this->makeResponse(
        $this->approvalDateChange->reject($id, $approvalType, $this->request->remark)
      );
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
}
