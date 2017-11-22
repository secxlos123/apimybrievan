<?php

namespace App\Http\Controllers\API\v1\Eks;

use App\Models\User;
use App\Models\Favourite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Favourite\CreateRequest;

class FavouriteController extends Controller
{

    /**
     * Show favourite base on customer
     * @param  Request $request
     * @param  int  $customerId
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $customerId)
    {
      $user = User::findOrFail($customerId);
      return response()->success([
        'contents' => $user->favourite
      ]);
    }

    /**
     * Store favourite customer
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function store(CreateRequest $request)
    {
      $request->request->add(['user_id' => $request->user()->id]);
      $favourite = Favourite::firstOrNew($request->only(['user_id', 'property_id']));
      $favourite->is_like = !$favourite->is_like;
      $favourite->save();
      return response()->success([
        'contents' => $favourite
      ]);
    }
}
