<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserFormRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserFormRequest  $userFormRequest
     * @return \App\Http\Resources\UserResource
     */
    public function store(UserFormRequest $userFormRequest)
    {
        $user = new User();
        $user->first_name = $userFormRequest->first_name;
        $user->last_name = $userFormRequest->last_name;
        $user->password = $userFormRequest->password;
        $user->uin = $userFormRequest->uin;
        $user->email = $userFormRequest->email;
        $user->name = $userFormRequest->name;
        $user->save();
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserFormRequest  $userFormRequest
     * @param  int  $id
     * @return \App\Http\Resources\UserResource
     */
    public function update(UserFormRequest $userFormRequest, $id)
    {
        $user = User::updateOrCreate(
            ['id' => $id],
            $userFormRequest->toArray()
        );
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
