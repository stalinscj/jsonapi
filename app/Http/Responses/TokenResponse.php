<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Contracts\Support\Responsable;

class TokenResponse implements Responsable
{
    /**
     * The user who own the token
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Create a new TokenResponse instance.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return response()->json([
            'plain-text-token' => $this->user->createToken($request->device_name)->plainTextToken
        ]);

    }
}
