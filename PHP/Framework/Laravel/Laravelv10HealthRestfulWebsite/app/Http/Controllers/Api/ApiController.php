<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class ApiController extends Controller
{
    public function isAdminAuthorization()
    {
        $user = auth()->user();
        return $user instanceof User && $user->hasRole('admin');
    }

    public function userLoggedIn(): ?User
    {
        $user = auth()->user();
        if ($user instanceof User) {
            return $user;
        }

        return null;
    }
}
