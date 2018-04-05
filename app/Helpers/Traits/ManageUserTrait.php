<?php

namespace App\Helpers\Traits;

use App\Http\Requests\API\v1\ActivedRequest;
use App\Models\User;

trait ManageUserTrait
{
	/**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show( User $model )
    {
        $model = User::getResponse( $model );
        return response()->success( [ "contents" => $model ] );
    }

	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $id
     * @return \Illuminate\Http\Response
     */
    public function actived( ActivedRequest $request, User $model )
    {
        $is_actived = $request->input( "is_actived" ) ? "aktifkan" : "non aktifkan";
        $model->update( $request->input() );
        return response()->success(
            [
                "message" => "Data {$this->activedFor} berhasil di {$is_actived}."
                , "contents" => User::getResponse( $model )
            ]
        );
    }

    /**
     * Store update user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array|\App\Models\User  $user
     * @return array|false
     */
    public function storeUpdate( $request, $model )
    {
        \DB::beginTransaction();
        try {
            $model = User::createOrUpdate( $request, $model, $this->relation );
            \DB::commit();
            return User::getResponse( $model );

        } catch ( \Exception $e ) {
            \DB::rollback();
            return false;

        }
    }
}