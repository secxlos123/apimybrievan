<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TempUser;
use App\Http\Requests\API\v1\Profile\UpdateRequest;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $profile = User::getProfile($request);
        return response()->success(['contents' => $profile]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request)
    {
        $user = $request->user();
        
        if ($user->inRole('developer') || $user->inRole('others')) {
            $request->merge(['user_id' => $user->id]);
            $temp = TempUser::updateOrCreate(['user_id' => $user->id], $request->all());
        }

        return response()->success(['message' => 'Data profile berhasil dirubah.']);
    }
}
