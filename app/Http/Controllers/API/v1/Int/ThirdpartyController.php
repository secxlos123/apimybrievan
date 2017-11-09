<?php

namespace App\Http\Controllers\API\v1\Int;

use App\Helpers\Traits\ManageUserTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\ThirdParty\CreateRequest;
use App\Http\Requests\API\v1\ThirdParty\UpdateRequest;
use App\Models\ThirdParty;
use App\Models\User;
use Illuminate\Http\Request;

class ThirdpartyController extends Controller
{
    use ManageUserTrait;

    /**
     * {@inheritDoc}
     */
    protected $activedFor = 'others';

    /**
     * {@inheritDoc}
     */
    protected $relation = 'thirdparty';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ?: 10;
        $thirdparty = ThirdParty::GetLists($request)->paginate($limit);
            return response()->success([
                'contents' => $thirdparty,
            ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {

        $save = $this->storeUpdate($request, []);
        if ($save) {
            return response()->success([
                'message' => 'Data pihak ke-3 berhasil ditambah.',
                'contents' => $save,
            ], 200);
        }

        return response()->error([
            'message' => 'Data pihak ke-3 Tidak Dapat Ditambah.',
        ], 500);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, User $thirdparty)
    {

        $user = $this->storeUpdate($request, $thirdparty);
        if ($user) {
            return response()->success(['message' => 'Data Pihak ke-3 berhasil dirubah.', 'contents' => $user]);
        }

        return response()->error(['message' => 'Maaf server sedang gangguan.'], 500);

    }

}
