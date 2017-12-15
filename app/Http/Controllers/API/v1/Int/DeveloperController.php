<?php

namespace App\Http\Controllers\API\v1\Int;

use App\Events\Developer\CreateOrUpdate;
use App\Helpers\Traits\ManageUserTrait;
use App\Http\Controllers\API\v1\Eks\PropertyController;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Developer\CreateRequest;
use App\Http\Requests\API\v1\Developer\UpdateRequest;
use App\Models\Developer;
use App\Models\User;
use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    use ManageUserTrait;

    /**
     * {@inheritDoc}
     */
    protected $activedFor = 'developer';

    /**
     * {@inheritDoc}
     */
    protected $relation = 'developer';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('has.user.dev', ['except' => ['index', 'store'] ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id = null)
    {
        $limit = $request->input('limit') ?: 10;
        if (!$id) {
          $developers = Developer::getLists($request)->paginate($limit);
        } else {
          $developers = Developer::where('id', $id)->get();
        }
        \Log::info($developers);
        $developers->transform(function ($developer) {
            $temp = $developer->toArray();
            if (!empty($developer->image)) {
                $temp['image'] = asset('img/noimage.jpg');

                if (file_exists(public_path("uploads/avatars/{$developer->image}"))) {
                    $temp['image'] = url("uploads/avatars/{$developer->image}");

                }

                if (file_exists(public_path("uploads/{$developer->image}"))) {
                    $temp['image'] = url("uploads/{$developer->image}");
                }
            }else{
                $temp['image'] = asset('img/noimage.jpg');
            }

            \Log::info($temp);
            return $temp;
        });

        \Log::info($developers);
        return response()->success(['contents' => !$id ? $developers : $developers->first()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        $user = $this->storeUpdate($request, []);
        if ($user) return $this->redirectTo($user, 'disimpan');
        return response()->error(['message' => 'Maaf server sedang gangguan.'], 500);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $developer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, User $developer)
    {
        $user = $this->storeUpdate($request, $developer);
        if ($user) return $this->redirectTo($user, 'dirubah');
        return response()->error(['message' => 'Maaf server sedang gangguan.'], 500);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function properties(Request $request, User $model)
    {
        return app(PropertyController::class)->index($request, $model->id);
    }

    /**
     * Return if this store or update success
     *
     * @param  array $user   [description]
     * @param  string $method [description]
     * @return \Illuminate\Http\Response
     */
    public function redirectTo($user, $method)
    {
        // event( new CreateOrUpdate ( $user['developer'] ) );
        \DB::beginTransaction();
        try {
        $insert = $this->sentDeveloperToCelas($user);
        $developer = Developer::findOrFail($user['developer']['id']);
        if($insert['code'] ==  '200' ){
                $developer->update(['dev_id_bri' => $insert['contents']]);
                 \DB::commit();
                return response()->success([
                'message'  => "Data developer berhasil {$method}.",
                'contents' => array_except($user, 'developer')
            ]);
        }
        else
        {
                \Log::info($developer->user_id);
                User::destroy($developer->user_id);
                \DB::commit();
                return response()->error([
                'message'  => "Data developer Gagal {$method}.",
                'contents' => array_except($user, 'developer')
            ]);
        }
        }catch (\Exception $e) {
            \Log::info('====================== catch Developer =====================');
            \Log::info($e);
            \DB::rollBack();
           return response()->error([
                'message'  => "Data developer Gagal Di Tambah.",
            ]);
        }
    }

    /**
     * Sent Developer Data To Celas
     * @param  [jsonobject] $user [user in request]
     * @return array response Celas
     */
    public function sentDeveloperToCelas($user)
    {
       $developer =  $user['developer'];

        $current = [
            'tipe_pihak_ketiga' => "DEVELOPER",
            'nama_pihak_ketiga' => $developer->company_name,
            'alamat_pihak_ketiga' => $developer->address,
            'pic_pihak_ketiga' => $developer->user->fullname,
            'pks_pihak_ketiga' => $developer->pks_number,
            'deskripsi_pihak_ketiga' => $developer->summary,
            'telepon_pihak_ketiga' => $developer->user->phone,
            'hp_pihak_ketiga' => $developer->user->mobile_phone,
            'fax_pihak_ketiga' => "",
            'deskripsi_pks_pihak_ketiga' => $developer->pks_description,
            'plafon_induk_pihak_ketiga' => $developer->plafond,
            'grup_sub_pihak_ketiga' => "null",
            'pihak_ketiga_value' => $developer->dev_id_bri ?: '',
        ];

        $id = \Asmx::setEndpoint('InsertDataPihakKetiga')
            ->setBody(['request' => json_encode($current)])
            ->post('form_params');
        \Log::info('================== response  Celas Add Developer=========');
        \Log::info($id);

        return $id;

    }
}
