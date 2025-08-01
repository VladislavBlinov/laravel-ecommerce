<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function show()
    {
        return UserResource::make(auth()->user());
    }

    public function update(UserRequest $request)
    {
        $user = auth()->user();
        $user->update($request->all());

        return UserResource::make($user);
    }
}
