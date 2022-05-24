<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Returns categories of autenticated user
     *
     * @return JsonResponse
     */
    public function getCategories()
    {
        return response()->json(Auth::user()->categories);
    }

    /**
     *
     */
    public function becomeHelper(Request $request)
    {

    }
}
